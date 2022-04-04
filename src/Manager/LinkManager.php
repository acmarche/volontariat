<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/10/18
 * Time: 14:39
 */

namespace AcMarche\Volontariat\Manager;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;

class LinkManager
{

    /**
     * @var Volontaire|null $volontaire
     */
    protected $volontaire;

    /**
     * @var Association|null $association
     */
    protected $association;

    /**
     * @var User[]|iterable
     */
    protected $users;

    public function getVolontaire(): ?Volontaire
    {
        return $this->volontaire;
    }

    public function setVolontaire(?Volontaire $volontaire): void
    {
        $this->volontaire = $volontaire;
    }

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): void
    {
        $this->association = $association;
    }

    /**
     * @return User[]|iterable
     */
    public function getUsers(): iterable
    {
        return $this->users;
    }

    /**
     * @param User[]|iterable $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
    }



}