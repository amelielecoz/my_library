<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Repository\BookRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture
{
    public function __construct(private readonly BookRepository $bookRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $books = $this->bookRepository->findAll();

        foreach ($books as $book) {
            for ($i = 0; $i < $faker->numberBetween(0, 5); $i++) {
                $comment = new Comment();
                $comment->setTitle($faker->sentence($faker->numberBetween(3, 7)));
                $comment->setText($faker->paragraph(5));
                $comment->setAuthor($faker->name());
                $comment->setBook($book);

                $dateTimeImmutable = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('- 1 years'));
                $comment->setCreatedAt($dateTimeImmutable);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
