<?php

namespace AcMarche\Volontariat\Spam;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CaptchaApi
{
    public const SESSION_NAME = 'sepul_comment';
    private HttpClientInterface $client;

    public function __construct()
    {
        $this->client = HttpClient::create();
    }


    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getDog(): string
    {
        $url = 'https://dog.ceo/api/breeds/image/random';
        $response = $this->client->request('GET', $url);

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $content['message'];
    }

    public function getCat(): string
    {
        $number = random_int(1, 16);

        $url = 'https://api.thecatapi.com/v1/images/search';
        $response = $this->client->request('GET', $url);

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (is_array($content)) {
            if (isset($content[0]['url'])) {
                return $content[0]['url'];
            }
        }

        return 'https://placekitten.com/150/150?image='.$number;
    }

    /**
     * @return string[]
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getAnimals(): array
    {
        $animals = [$this->getDog(), $this->getCat()];
        shuffle($animals);

        return $animals;
    }

    public function getObjects(): void
    {
        // https://picsum.photos/seed/picsum/200/300
        // https://source.unsplash.com/random/200/300
    }
}
