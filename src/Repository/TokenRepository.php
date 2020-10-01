<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Entity\Security\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Token|null find($id, $lockMode = null, $lockVersion = null)
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    public function insert(Token $token)
    {
        $this->persist($token);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function remove(Token $token)
    {
        $this->_em->remove($token);
        $this->save();
    }

    public function persist(Token $token)
    {
        $this->_em->persist($token);
    }
}
