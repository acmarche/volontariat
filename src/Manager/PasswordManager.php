<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/11/18
 * Time: 12:03
 */

namespace AcMarche\Volontariat\Manager;

use AcMarche\Volontariat\Entity\Security\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordManager
{
    public function __construct(private UserPasswordHasherInterface $userPasswordEncoder)
    {
    }

    public function changePassword(User $user, string $plainPassword): void
    {
        $passwordCrypted = $this->userPasswordEncoder->hashPassword($user, $plainPassword);
        $user->setPassword($passwordCrypted);
        $user->setPlainPassword($plainPassword);//pour envoie par mail
    }

    public function cryptPassword(User $user): void
    {
        $passwordCrypted = $this->userPasswordEncoder->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($passwordCrypted);
    }
}