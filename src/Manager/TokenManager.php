<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 31/10/18
 * Time: 13:03
 */

namespace AcMarche\Volontariat\Manager;

use AcMarche\Volontariat\Entity\Security\Token;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Repository\TokenRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Security\AppAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class TokenManager
{
    /**
     * @var TokenRepository
     */
    private $tokenRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var GuardAuthenticatorHandler
     */
    private $guardAuthenticatorHandler;
    /**
     * @var AppAuthenticator
     */
    private $appAuthenticator;

    public function __construct(
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        AppAuthenticator $appAuthenticator,
        TokenRepository $tokenRepository,
        UserRepository $userRepository
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->userRepository = $userRepository;
        $this->guardAuthenticatorHandler = $guardAuthenticatorHandler;
        $this->appAuthenticator = $appAuthenticator;
    }

    public function getInstance(User $user)
    {
        if (!$token = $this->tokenRepository->findOneBy(['user' => $user])) {
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
        } catch (\Exception $e) {
        }

        $expireTime = new \DateTime('+90 day');
        $token->setExpireAt($expireTime);

        $this->tokenRepository->save();

        return $token;
    }

    public function isExpired(Token $token)
    {
        $today = new \DateTime('today');

        return $today > $token->getExpireAt();
    }

    public function createForAllUsers()
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->generate($user);
        }
    }

    public function loginUser(Request $request, User $user, $firewallName)
    {
        $this->guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $this->appAuthenticator,
            $firewallName
        );
    }
}