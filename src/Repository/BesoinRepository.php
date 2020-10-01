<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Entity\Besoin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Besoin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Besoin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Besoin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BesoinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Besoin::class);
    }


    public function insert(Besoin $besoin)
    {
        $this->_em->persist($besoin);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function remove(Besoin $besoin)
    {
        $this->_em->remove($besoin);
        $this->save();
    }

    /**
     * @param int $max
     * @return Besoin[]
     */
    public function getRecent($max = 5)
    {
        $now = new \DateTime();

        $qb = $this->createQueryBuilder('so');
        $qb->leftJoin('so.association', 'association', 'WITH');
        $qb->addSelect('association');

        $qb->andwhere('so.date_end >= :date ')
            ->setParameter('date', $now);

        $qb->setMaxResults($max);
        $qb->addOrderBy('so.date_begin', 'DESC');

        $query = $qb->getQuery();
        $results = $query->getResult();

        return $results;
    }
}
