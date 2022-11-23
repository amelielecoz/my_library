<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\ReservationRequest;
use App\Form\CommentFormType;
use App\Form\ReservationRequestFormType;
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
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setBook($book);

            $this->entityManager->persist($comment);
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



        return new Response($twig->render('book/show.html.twig', [
            'book' => $book,
            'comment_form' => $commentForm->createView(),
            'reservation_request_form' => $reservationRequestForm->createView(),
        ]));
    }

}
