<?php

namespace AcMarche\Volontariat\Security;

use AcMarche\Volontariat\Entity\Security\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\ByteString;

class PasswordGenerator
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordEncoder
    ) {
    }

    public function generate(): string
    {
        return ByteString::fromRandom(8);
    }

    public function cryptPassword(User $user, string $plainPassword): string
    {
        return $this->userPasswordEncoder->hashPassword($user, $plainPassword);
    }

    public function generateAndCrypt(User $user): string
    {
        return $this->cryptPassword($user, $this->generate());
    }
}