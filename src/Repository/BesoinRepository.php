<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Doctrine\OrmCrudTrait;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Besoin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Besoin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Besoin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Besoin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BesoinRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Besoin::class);
    }

    /**
     * @param int $max
     *
     * @return Besoin[]
     */
    public function getRecent($max = 5): array
    {
        $now = new \DateTime();

        $qb = $this->createQueryBuilder('so');
        $qb->leftJoin('so.association', 'association', 'WITH');
        $qb->addSelect('association');

        $qb
            ->andWhere('so.date_end >= :date ')
            ->setParameter('date', $now);

        $qb->setMaxResults($max);
        $qb->addOrderBy('so.date_begin', 'DESC');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @return Besoin[]
     */
    public function findByAssociation(?Association $association): array
    {
        return $this
            ->createQueryBuilder('besoin')
            ->andWhere('besoin.association = :association')
            ->setParameter('association', $association)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Besoin[]
     */
    public function search(string $keyword): array
    {
        return $this
            ->createQueryBuilder('besoin')
            ->andWhere(
                'besoin.name LIKE :mot OR besoin.requirement LIKE :mot OR besoin.place LIKE :mot ',
            )
            ->setParameter('mot', '%'.$keyword.'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Use MapEntity url
     * @param string $uuid
     * @return Besoin|null
     */
    public function findOneByUuid(string $uuid): ?Besoin
    {
        return $this
            ->createQueryBuilder('besoin')
            ->andWhere('besoin.uuid = :uuid')
            ->setParameter('uuid', $uuid, ParameterType::STRING)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
