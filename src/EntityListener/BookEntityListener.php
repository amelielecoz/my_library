<?php

namespace App\EntityListener;

use App\Entity\Book;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookEntityListener
{
    function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function prePersist(Book $book): void
    {
        $book->computeSlug($this->slugger);
    }

    public function preUpdate(Book $book): void
    {
        $book->computeSlug($this->slugger);
    }
}