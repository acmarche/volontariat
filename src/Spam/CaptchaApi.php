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

    private HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
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
        $response = $this->httpClient->request('GET', $url);

        $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $content['message'];
    }

    public function getCat(): string
    {
        $number = random_int(1, 16);

        try {
            $url = 'https://api.thecatapi.com/v1/images/search';
            $response = $this->httpClient->request('GET', $url);

            $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
            if (is_array($content) && isset($content[0]['url'])) {
                return $content[0]['url'];
            }
        } catch (\Exception) {
            // fallback on rate limit or any API error
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
