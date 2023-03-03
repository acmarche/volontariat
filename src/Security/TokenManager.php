<?php

namespace AcMarche\Volontariat\Security;

use AcMarche\Volontariat\Entity\Security\Token;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Repository\TokenRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class TokenManager
{
    public function __construct(
        private UserAuthenticatorInterface $userAuthenticator,
        private FormLoginAuthenticator $formLoginAuthenticator,
        private TokenRepository $tokenRepository,
        private UserRepository $userRepository
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

    public function generate(User $user)
    {
        $token = $this->getInstance($user);
        try {
            $token->setValue(bin2hex(random_bytes(20)));
        } catch (Exception) {
        }

        $expireTime = new DateTime('+90 day');
        $token->setExpireAt($expireTime);

        $this->tokenRepository->save();

        return $token;
    }

    public function isExpired(Token $token): bool
    {
        $today = new DateTime('today');

        return $today > $token->getExpireAt();
    }

    public function createForAllUsers(): void
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->generate($user);
        }
    }

    public function loginUser(Request $request, User $user, $firewallName): void
    {
        $this->userAuthenticator->authenticateUser(
            $user,
            $this->formLoginAuthenticator,
            $request,
        );
    }

}