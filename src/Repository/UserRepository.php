<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function insert(User $user)
    {
        $this->_em->persist($user);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function remove(User $user)
    {
        $this->_em->remove($user);
        $this->save();
    }
}
