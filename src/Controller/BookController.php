<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class BookController extends AbstractController
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        $books = $this->bookRepository->findAll();

        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
            'books' => $books,
        ]);
    }

    #[Route('/book/{slug}', name: 'app_book_show')]
    public function show(Request $request, Environment $twig, Book $book): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBook($book);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_book_show', ['slug' => $book->getSlug()]);
        }

        return new Response($twig->render('book/show.html.twig', [
            'book' => $book,
            'comment_form' => $form->createView(),
        ]));
    }

}
