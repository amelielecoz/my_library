<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(private readonly AuthorRepository $authorRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $authors = $this->authorRepository->findAll();

        for ($i = 0; $i < 10; $i++) {
            $book = new Book();

            $book->setTitle($faker->sentence($faker->numberBetween(3, 6)));
            $isbn13 = $faker->ean13();
            $book->setIsbn(substr($isbn13, 3));
            $book->setIsbn13($isbn13);
            $book->setSummary($faker->paragraph($faker->numberBetween(3, 5)));
            $book->setIsAvailable($faker->boolean(80));

            $count = $faker->numberBetween(1, 4);

            for ($j = 0; $j < $count; $j++) {
                $book->addAuthor($faker->randomElement($authors));
            }

            $manager->persist($book);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
