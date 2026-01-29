<?php

namespace AcMarche\Volontariat\Security;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use DateTime;
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
        private UserRepository $userRepository,
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private RouterInterface $router
    ) {
    }

    public function generate(UserInterface $user, ?DateTime $expireAt = null): ?string
    {
        if (!$expireAt instanceof DateTime) {
            $expireAt = new DateTime('+90 day');
        }

        if ($user instanceof Association || $user instanceof Volontaire || $user instanceof User) {
            try {
                $user->tokenValue = bin2hex(random_bytes(20));
            } catch (Exception) {
            }
            $user->tokenExpireAt = $expireAt;
            $this->flush($user);

            return $user->tokenValue;
        }

        return null;
    }

    public function isExpired(?UserInterface $entity): bool
    {
        $today = new DateTime('today');

        if (($entity instanceof Association || $entity instanceof Volontaire || $entity instanceof User)) {
            return $today > $entity->tokenExpireAt;
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

        if ($user = $this->userRepository->findOneByTokenValue($value)) {
            return $user;
        }

        return null;
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
