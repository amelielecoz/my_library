<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Repository\BookRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(private readonly BookRepository $bookRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $books = $this->bookRepository->findAll();

        foreach ($books as $book) {
            $count = $faker->numberBetween(0, 5);
            for ($i = 0; $i < $count; $i++) {
                $comment = new Comment();
                $comment->setTitle($faker->realText(40));
                $comment->setText($faker->realText(600));
                $comment->setAuthor($faker->name());
                $comment->setEmail($faker->email());
                $comment->setState('published');
                $comment->setBook($book);

                $dateTimeImmutable = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('- 1 years'));
                $comment->setCreatedAt($dateTimeImmutable);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
