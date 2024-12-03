<?php

namespace AcMarche\Volontariat\Security;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\Token;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\TokenRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;

class TokenManager
{
    public function __construct(
        private UserAuthenticatorInterface $userAuthenticator,
        private AuthenticatorInterface $formLoginAuthenticator,
        private TokenRepository $tokenRepository,
        private UserRepository $userRepository,
        private RouterInterface $router
    ) {
    }

    public function getInstance(User $user): Token
    {
        if (($token = $this->tokenRepository->findOneBy(['user' => $user])) === null) {
            $token = new Token();
            $token->setUser($user);
            $this->tokenRepository->persist($token);
        }

        return $token;
    }

    public function generate(User $user, \DateTime $expireAt = null): Token
    {
        $token = $this->getInstance($user);
        try {
            $token->setValue(bin2hex(random_bytes(20)));
        } catch (Exception) {
        }
        if (!$expireAt) {
            $expireAt = new \DateTime('+90 day');
        }
        $token->setExpireAt($expireAt);

        $this->tokenRepository->flush();

        return $token;
    }

    public function isExpired(Token $token): bool
    {
        $today = new \DateTime('today');

        return $today > $token->getExpireAt();
    }

    public function createForAllUsers(): void
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->generate($user);
        }
        $this->userRepository->flush();
    }

    public function loginUser(Request $request, User $user, $firewallName): void
    {
        $this->userAuthenticator->authenticateUser(
            $user,
            $this->formLoginAuthenticator,
            $request,
        );
    }

    public function getLinkToConnect(Association|Volontaire $entity): ?string
    {
        if (!$user = $entity->user) {
            return null;
        }

        if (!$token = $this->getInstance($user)) {
            return null;
        }

        return $this->router->generate(
            'volontariat_token_show',
            ['value' => $token->getValue()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
