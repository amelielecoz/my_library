<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuthorFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 30; $i++) {
            $author = new Author();

            $author->setFirstName($faker->firstName());
            $author->setLastName($faker->lastName());

            $manager->persist($author);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
