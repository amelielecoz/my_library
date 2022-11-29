<?php

namespace App;

use Doctrine\DBAL\Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function PHPUnit\Framework\throwException;

class IsbndbClient
{
    private $client;
    private $endpoint;

    public function __construct(HttpClientInterface $client, private readonly string $isbndbKey)
    {
        $this->client = $client;
        $this->endpoint = 'https://api2.isbndb.com/book/';
    }

    public function findBook(string $isbn): array
    {

        $response = $this->client->request(
            'GET',
            $this->endpoint.$isbn,
            [
                'headers' => [
                    'Authorization' => $this->isbndbKey,
                ],
            ]
        );

        if ($response->getStatusCode() === 404) {
            throwException(new Exception('Book not found'));
        }

        if ($response->getStatusCode() !== 200) {
            throwException(new Exception('API call did not work. Please contact administrators.'));
        }

        return json_decode($response->getContent(), true)['book'];
    }

}

