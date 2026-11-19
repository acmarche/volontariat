<?php

namespace AcMarche\Volontariat\Security;

use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class EmailUniquenessChecker
{
    public function __construct(
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function isEmailAvailable(string $email, ?UserInterface $excludeEntity = null): bool
    {
        $user = $this->userRepository->findOneByEmail($email);
        if ($user && $user !== $excludeEntity) {
            return false;
        }
        $association = $this->associationRepository->findOneByEmail($email);
        if ($association && $association !== $excludeEntity) {
            return false;
        }

        $volontaire = $this->volontaireRepository->findOneByEmail($email);
        if ($volontaire && $volontaire !== $excludeEntity) {
            return false;
        }


        return true;
    }

    public function findByEmail(string $email): ?UserInterface
    {
        if ($user = $this->userRepository->findOneByEmail($email)) {
            return $user;
        }

        if ($association = $this->associationRepository->findOneByEmail($email)) {
            return $association;
        }

        if ($volontaire = $this->volontaireRepository->findOneByEmail($email)) {
            return $volontaire;
        }


        return null;
    }
}
