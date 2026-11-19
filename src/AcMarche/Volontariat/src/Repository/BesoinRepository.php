<?php

namespace AcMarche\Volontariat\Repository;

use DateTime;
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
 * @extends ServiceEntityRepository<Besoin>
 */
class BesoinRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Besoin::class);
    }

    /**
     * @param int $max
     *
     * @return Besoin[]
     */
    public function getRecent(?int $max = 5): array
    {
        $now = new DateTime();

        $queryBuilder = $this->createQueryBuilder('so');
        $queryBuilder->leftJoin('so.association', 'association', 'WITH');
        $queryBuilder->addSelect('association');

        $queryBuilder
            ->andWhere('so.date_end >= :date ')
            ->setParameter('date', $now);

        $queryBuilder->setMaxResults($max);
        $queryBuilder->addOrderBy('so.date_begin', 'DESC');

        $query = $queryBuilder->getQuery();

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
