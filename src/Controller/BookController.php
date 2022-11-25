<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\ReservationRequest;
use App\Form\CommentFormType;
use App\Form\ReservationRequestFormType;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class BookController extends AbstractController
{
    public function __construct(
        private readonly BookRepository         $bookRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Environment            $twig,
        private readonly CommentRepository      $commentRepository
    )
    {
    }

    #[Route('/', name: 'app_book')]
    public function index(): Response
    {
        $books = $this->bookRepository->findAll();

        return new Response($this->twig->render('book/index.html.twig', [
            'controller_name' => 'BookController',
            'books' => $books,
        ]));
    }

    #[Route('/book/{slug}', name: 'app_book_show')]
    public function show(
        Request $request,
        Book $book,
        SpamChecker $spamChecker,
    ): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->commentRepository->getCommentPaginator($book, $offset);

        $comment = new Comment();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setBook($book);

            $this->entityManager->persist($comment);

            $context = [
                'user_ip' => $request->getClientIp(),
                'user_agent' => $request->headers->get('user-agent'),
                'referrer' => $request->headers->get('referer'),
                'permalink' => $request->getUri(),
            ];

            if (2 === $spamChecker->getSpamScore($comment, $context)) {
                throw new \RuntimeException('Blatant spam, go away!');}
            $this->entityManager->flush();

            return $this->redirectToRoute('app_book_show', ['slug' => $book->getSlug()]);
        }

        $reservationRequest = new ReservationRequest();
        $reservationRequestForm = $this->createForm(ReservationRequestFormType::class, $reservationRequest);
        $reservationRequestForm->handleRequest($request);

        if ($reservationRequestForm->isSubmitted() && $reservationRequestForm->isValid()) {
            $reservationRequest->setBook($book);

            $this->entityManager->persist($reservationRequest);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_book_show', ['slug' => $book->getSlug()]);
        }

        return new Response($this->twig->render('book/show.html.twig', [
            'book' => $book,
            'comment_form' => $commentForm->createView(),
            'reservation_request_form' => $reservationRequestForm->createView(),
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]));
    }
}
