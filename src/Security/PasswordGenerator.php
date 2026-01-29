<?php

namespace AcMarche\Volontariat\Security;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\ByteString;

class PasswordGenerator
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function generate(): string
    {
        return ByteString::fromRandom(8);
    }

    public function cryptPassword(UserInterface $user, string $plainPassword): string
    {
        return $this->userPasswordHasher->hashPassword($user, $plainPassword);
    }

    public function generateAndCrypt(UserInterface $user): string
    {
        return $this->cryptPassword($user, $this->generate());
    }
}
