<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'List of books');
    }

    public function testBookPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertCount(2, $crawler->filter('h1'));

        $client->clickLink('View');

        $this->assertPageTitleContains('My first book');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'My first book');
        $this->assertSelectorExists('div:contains("There are 1 comments")');
    }
}
