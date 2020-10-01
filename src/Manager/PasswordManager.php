<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/11/18
 * Time: 12:03
 */

namespace AcMarche\Volontariat\Manager;

use AcMarche\Volontariat\Entity\Security\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordManager
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function changePassword(User $user, string $plainPassword)
    {
        $passwordCrypted = $this->userPasswordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($passwordCrypted);
        $user->setPlainPassword($plainPassword);//pour envoie par mail
    }

    public function cryptPassword(User $user)
    {
        $passwordCrypted = $this->userPasswordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($passwordCrypted);
    }
}