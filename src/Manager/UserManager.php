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
    public function __construct(private UserRepository $userRepository, private PasswordManager $passwordManager)
    {
    }

    public function newInstance(): User
    {
        return new User();
    }

    public function insert(User $user): void
    {
        $user->setEmail($user->getEmail());
        $user->setRoles([SecurityData::getRoleVolontariat()]);
        $this->passwordManager->cryptPassword($user);
        $this->userRepository->insert($user);
    }

    public function save(): void
    {
        $this->userRepository->save();
    }

    public function delete(User $user): void
    {
        $this->userRepository->remove($user);
    }

    /**
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function updateUser(): void
    {
        $this->userRepository->save();
    }

}