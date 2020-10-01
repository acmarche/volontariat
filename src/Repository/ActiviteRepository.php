<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Entity\Activite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Activite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActiviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activite::class);
    }

    public function insert(Activite $activite)
    {
        $this->_em->persist($activite);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function remove(Activite $activite)
    {
        $this->_em->remove($activite);
        $this->save();
    }
}
