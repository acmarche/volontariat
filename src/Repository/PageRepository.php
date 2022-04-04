<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /**
     * @return array|Page[]
     */
    public function findRecent(): array
    {
        return $this->createQueryBuilder('page')
            ->andWhere('page.actualite = 1')
            ->orderBy('page.id', 'DESC')->getQuery()->getResult();
    }

    public function insert(Page $page): void
    {
        $this->_em->persist($page);
        $this->save();
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function remove(Page $page): void
    {
        $this->_em->remove($page);
        $this->save();
    }
}
