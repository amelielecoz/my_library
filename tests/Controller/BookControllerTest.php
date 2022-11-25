<?php

namespace App\Tests\Controller;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function testCommentSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/book/my-first-book');
        $client->submitForm('Submit', [
            'comment_form[author]' => 'Fabien',
            'comment_form[title]' => 'Some feedback from an automated functional test',
            'comment_form[text]' => 'Test text',
            'comment_form[email]' => $email = 'me@automat.ed',
        ]);
        $this->assertResponseRedirects();

        //simulate comment validation
        $comment = self::getContainer()->get(CommentRepository::class)->findOneByEmail($email);
        $comment->setState('published');
        self::getContainer()->get(EntityManagerInterface::class)->flush();

        $client->followRedirect();
        $this->assertSelectorExists('div:contains("There are 2 comments")');
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
