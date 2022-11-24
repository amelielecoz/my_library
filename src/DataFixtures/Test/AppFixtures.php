<?php

namespace App\DataFixtures\Test;

use App\Entity\Book;
use App\Entity\Comment;
use App\Repository\AuthorRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $myFirstBook = new Book();

        $myFirstBook->setTitle('My first book');
        $isbn13 = '1234567890123';
        $myFirstBook->setIsbn(substr($isbn13, 3));
        $myFirstBook->setIsbn13($isbn13);
        $myFirstBook->setSummary($faker->paragraph($faker->numberBetween(3, 5)));
        $myFirstBook->setIsAvailable($faker->boolean(80));
        $manager->persist($myFirstBook);

        $mySecondBook = new Book();
        $mySecondBook->setTitle('My second book');
        $isbn13 = '4567890123456';
        $mySecondBook->setIsbn(substr($isbn13, 3));
        $mySecondBook->setIsbn13($isbn13);
        $mySecondBook->setSummary($faker->paragraph($faker->numberBetween(3, 5)));
        $mySecondBook->setIsAvailable($faker->boolean(80));
        $manager->persist($mySecondBook);

        $comment1 = new Comment();
        $comment1->setBook($myFirstBook);
        $comment1->setAuthor('Amélie');
        $comment1->setEmail('amelie@example.com');
        $comment1->setTitle('This was a great conference.');
        $comment1->setText($faker->paragraph($faker->numberBetween(3, 5)));
        $manager->persist($comment1);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
