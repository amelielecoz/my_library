<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\ReservationRequest;
use App\Form\CommentFormType;
use App\Form\IsbnFormType;
use App\Form\ReservationRequestFormType;
use App\IsbndbClient;
use App\Message\CommentMessage;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;
use Twig\Environment;

#[Route('/admin')]
class AdminController extends AbstractController
{
    public function __construct(
        private readonly Environment            $twig,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $bus
    ) {
    }

    #[Route('/comment/review/{id}', name: 'review_comment')]
    public function reviewComment(Request $request, Comment $comment, Registry $registry): Response
    {
        $accepted = !$request->query->get('reject');

        $machine = $registry->get($comment);
        if ($machine->can($comment, 'publish')) {
            $transition = $accepted ? 'publish' : 'reject';
        } elseif ($machine->can($comment, 'publish_ham')) {
            $transition = $accepted ? 'publish_ham' : 'reject_ham';
        } else {
            return new Response('Comment already reviewed or not in the right state.');
        }

        $machine->apply($comment, $transition);
        $this->entityManager->flush();

        if ($accepted) {
            $this->bus->dispatch(new CommentMessage($comment->getId()));
        }

        return new Response($this->twig->render('admin/review.html.twig', [
            'transition' => $transition,
            'comment' => $comment,
        ]));
    }

    #[Route('/http-cache/{uri<.*>}', methods: ['PURGE'])]
    public function purgeHttpCache(KernelInterface $kernel, Request $request, string $uri, StoreInterface $store): Response
    {
        if ('prod' === $kernel->getEnvironment()) {
            return new Response('KO', 400);
        }

        $store->purge($request->getSchemeAndHttpHost().'/'.$uri);

        return new Response('Done');
    }

    #[Route('/book/add', name: 'admin_book_add')]
    public function show(
        Request $request,
        IsbndbClient $client,
        AuthorRepository $authorRepository,
        BookRepository $bookRepository,
    ): Response
    {
        $isbnForm = $this->createForm(IsbnFormType::class);
        $isbnForm->handleRequest($request);
        if ($isbnForm->isSubmitted() && $isbnForm->isValid()) {
            $isbn = $isbnForm['isbn']->getData();
            $book = $bookRepository->findOneBy(['isbn13' => $isbn]);

            if ($book) {
                $this->addFlash(
                    'notice',
                    'This book already exists in the library'
                );

                return $this->redirectToRoute('admin_book_add');
            }

            $data = $client->findBook($isbn);

            $book = new Book();
            $book->setTitle($data['title']);
            $book->setIsbn13($data['isbn13']);
            $book->setIsbn($data['isbn']);
            $book->setIsAvailable(true);
            $book->setImageUrl($data['image']);
            $book->setPublisher($data['publisher']);

            foreach ($data['authors'] as $authorNames) {
                if (str_contains($authorNames, ',')) {
                    $names = explode(',', $authorNames);
                    $lastName = trim($names[0]);
                    $firstName = trim($names[1]);
                } else {
                    $names = explode(' ', $authorNames);
                    $firstName = trim($names[0]);
                    $lastName = trim($names[1]);
                }

                $author = $authorRepository->findOneBy(['lastName' => $lastName, 'firstName' => $firstName]);

                if (!$author) {
                    $author = new Author();
                    $author->setLastName($lastName);
                    $author->setFirstName($firstName);

                    $this->entityManager->persist($author);
                    $this->entityManager->flush();
                }

                $book->addAuthor($author);
            }

            $this->entityManager->persist($book);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                sprintf('The book titled "%s" has correctly been added to the library.', $book->getTitle())
            );

            return $this->redirectToRoute('admin_book_add');
        }

        return new Response($this->twig->render('book/add.html.twig', [
            'isbn_form' => $isbnForm->createView(),
        ]));
    }
}