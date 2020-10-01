<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 22/08/18
 * Time: 13:17
 */

namespace AcMarche\Volontariat\Manager;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Security\SecurityData;

class UserManager
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PasswordManager
     */
    private $passwordManager;

    public function __construct(
        UserRepository $userRepository,
        PasswordManager $passwordManager
    ) {
        $this->userRepository = $userRepository;
        $this->passwordManager = $passwordManager;
    }

    public function newInstance(): User
    {
        return new User();
    }

    public function insert(User $user)
    {
        $user->setEmail($user->getEmail());
        $user->setRoles([SecurityData::getRoleVolontariat()]);
        $this->passwordManager->cryptPassword($user);
        $this->userRepository->insert($user);
    }

    public function save()
    {
        $this->userRepository->save();
    }

    public function delete(User $user)
    {
        $this->userRepository->remove($user);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail(string $email)
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function updateUser()
    {
        $this->userRepository->save();
    }

}