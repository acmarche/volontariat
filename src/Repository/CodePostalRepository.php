<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Doctrine\OrmCrudTrait;
use AcMarche\Volontariat\Entity\CodePostal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CodePostal|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodePostal|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodePostal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodePostalRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodePostal::class);
    }
}
