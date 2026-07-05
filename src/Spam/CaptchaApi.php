<?php

namespace AcMarche\Volontariat\Spam;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CaptchaApi
{
    public const SESSION_NAME = 'sepul_comment';

    private HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }


    public function getFox(): string
    {
        $number = random_int(1, 122);

        try {
            $url = 'https://randomfox.ca/floof/';
            $response = $this->httpClient->request('GET', $url);

            $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
            if (is_array($content) && isset($content['image'])) {
                return $content['image'];
            }
        } catch (\Exception) {
            // fallback on rate limit or any API error
        }

        return 'https://randomfox.ca/images/'.$number.'.jpg';
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

        return 'https://cataas.com/cat?width=150&height=150&_='.$number;
    }

    /**
     * @return string[]
     */
    public function getAnimals(): array
    {
        $animals = [$this->getFox(), $this->getCat()];
        shuffle($animals);

        return $animals;
    }

    public function getObjects(): void
    {
        // https://picsum.photos/seed/picsum/200/300
        // https://source.unsplash.com/random/200/300
    }
}
