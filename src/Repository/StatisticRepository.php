<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Doctrine\OrmCrudTrait;
use AcMarche\Volontariat\Entity\Enum\StatisticTypeEnum;
use AcMarche\Volontariat\Entity\Statistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Statistic>
 */
class StatisticRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Statistic::class);
    }

    public function countByTypeAndYear(StatisticTypeEnum $type, ?int $year): int
    {
        $qb = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->andWhere('s.type = :type')
            ->setParameter('type', $type);

        if ($year !== null) {
            $qb->andWhere('YEAR(s.createdAt) = :year')
                ->setParameter('year', $year);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countByTypeGroupedByMonth(StatisticTypeEnum $type, ?int $year): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('MONTH(s.createdAt) AS month, COUNT(s.id) AS total')
            ->andWhere('s.type = :type')
            ->setParameter('type', $type)
            ->groupBy('month')
            ->orderBy('month', 'ASC');

        if ($year !== null) {
            $qb->andWhere('YEAR(s.createdAt) = :year')
                ->setParameter('year', $year);
        }

        return $qb->getQuery()->getResult();
    }

    public function countAllGroupedByType(?int $year): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.type AS type, COUNT(s.id) AS total')
            ->groupBy('type');

        if ($year !== null) {
            $qb->andWhere('YEAR(s.createdAt) = :year')
                ->setParameter('year', $year);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<int, array{year: int}>
     */
    public function findDistinctYears(): array
    {
        return $this->createQueryBuilder('s')
            ->select('YEAR(s.createdAt) AS year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
