<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $book = new Book();

            $book->setTitle($faker->sentence($faker->numberBetween(3, 6)));
            $isbn13 = $faker->ean13();
            $book->setIsbn(substr($isbn13, 3));
            $book->setIsbn13($isbn13);
            $book->setSummary($faker->paragraph($faker->numberBetween(3, 5)));
            $book->setIsAvailable($faker->boolean(80));

            $manager->persist($book);
        }

        $manager->flush();
    }
}
