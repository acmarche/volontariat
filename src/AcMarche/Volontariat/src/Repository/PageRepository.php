<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Doctrine\OrmCrudTrait;
use AcMarche\Volontariat\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Page::class);
    }

    /**
     * @return array|Page[]
     */
    public function findRecentNews(): array
    {
        return $this->createQueryBuilder('page')
            ->andWhere('page.actualite = 1')
            ->setMaxResults(6)
            ->orderBy('page.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Page[]
     */
    public function search(string $keyword): array
    {
        return $this->createQueryBuilder('page')
            ->andWhere('page.actualite = 1')
            ->andWhere('page.title LIKE :mot OR page.content LIKE :mot OR page.excerpt LIKE :mot ')
            ->setParameter('mot', '%'.$keyword.'%')
            ->orderBy('page.id', 'DESC')
            ->getQuery()->getResult();
    }
}
