<?php

namespace AcMarche\Volontariat\Spam\Handler;

use AcMarche\Volontariat\Spam\CaptchaApi;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SpamHandler
{
    public function __construct(
        private readonly RateLimiterFactoryInterface $anonymousApiLimiter,
        private readonly CaptchaApi $captchaApi
    ) {
    }

    public function isAccepted(Request $request): bool
    {
        $limiter = $this->anonymousApiLimiter->create($request->getClientIp());

        return $limiter->consume()->isAccepted();
    }

    public function checkCaptcha(?string $value): bool
    {
        if (!$value) {
            return false;
        }

        return str_contains($value, 'thecatapi');
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function generate(): array
    {
        return $this->captchaApi->getAnimals();
    }
}
