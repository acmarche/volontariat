<?php

namespace AcMarche\Volontariat\Security;

use DateTime;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\Token;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\TokenRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;

class TokenManager
{
    public function __construct(
        private UserAuthenticatorInterface $userAuthenticator,
        private AuthenticatorInterface $formLoginAuthenticator,
        private TokenRepository $tokenRepository,
        private UserRepository $userRepository,
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private RouterInterface $router
    ) {
    }

    public function generate(UserInterface $user, ?DateTime $expireAt = null): Token|string
    {
        if (!$expireAt instanceof DateTime) {
            $expireAt = new DateTime('+90 day');
        }

        if ($user instanceof Association || $user instanceof Volontaire) {
            try {
                $user->tokenValue = bin2hex(random_bytes(20));
            } catch (Exception) {
            }
            $user->tokenExpireAt = $expireAt;
            $this->flush($user);

            return $user->tokenValue;
        }

        // User (admin) - use Token entity
        $token = $this->getInstanceForUser($user);
        try {
            $token->setValue(bin2hex(random_bytes(20)));
        } catch (Exception) {
        }
        $token->setExpireAt($expireAt);
        $this->tokenRepository->flush();

        return $token;
    }

    public function isExpired(Token|UserInterface $tokenOrEntity): bool
    {
        $today = new DateTime('today');

        if ($tokenOrEntity instanceof Token) {
            return $today > $tokenOrEntity->getExpireAt();
        }

        if (($tokenOrEntity instanceof Association || $tokenOrEntity instanceof Volontaire) && $tokenOrEntity->tokenExpireAt) {
            return $today > $tokenOrEntity->tokenExpireAt;
        }

        return true;
    }

    public function createForAllUsers(): void
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->generate($user);
        }

        $associations = $this->associationRepository->findAll();
        foreach ($associations as $association) {
            $this->generate($association);
        }

        $volontaires = $this->volontaireRepository->findAll();
        foreach ($volontaires as $volontaire) {
            $this->generate($volontaire);
        }
    }

    public function loginUser(Request $request, UserInterface $user, $firewallName): void
    {
        $this->userAuthenticator->authenticateUser(
            $user,
            $this->formLoginAuthenticator,
            $request,
        );
    }

    public function getLinkToConnect(Association|Volontaire $entity): ?string
    {
        if (!$entity->tokenValue) {
            return null;
        }

        return $this->router->generate(
            'volontariat_token_show',
            ['value' => $entity->tokenValue],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function findByTokenValue(string $value): ?UserInterface
    {
        if ($association = $this->associationRepository->findOneByTokenValue($value)) {
            return $association;
        }

        if ($volontaire = $this->volontaireRepository->findOneByTokenValue($value)) {
            return $volontaire;
        }

        $token = $this->tokenRepository->findOneBy(['value' => $value]);
        if ($token instanceof Token) {
            return $token->getUser();
        }

        return null;
    }

    private function getInstanceForUser(User $user): Token
    {
        if (($token = $this->tokenRepository->findOneBy(['user' => $user])) === null) {
            $token = new Token();
            $token->setUser($user);
            $this->tokenRepository->persist($token);
        }

        return $token;
    }

    private function flush(UserInterface $user): void
    {
        if ($user instanceof Association) {
            $this->associationRepository->flush();
        } elseif ($user instanceof Volontaire) {
            $this->volontaireRepository->flush();
        } else {
            $this->userRepository->flush();
        }
    }
}
