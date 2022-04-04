<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 14/11/18
 * Time: 11:28
 */

namespace AcMarche\Volontariat\Security;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Exception\AccountAccordException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * Checks the user account before authentication.
     *
     * @throws AccountStatusException
     */
    public function checkPreAuth(UserInterface $user): bool
    {
        return true;
    }

    /**
     * Checks the user account after authentication.
     *
     * @throws AccountStatusException
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getAccord()) {
            throw new AccountAccordException(' accord');
        }
    }
}