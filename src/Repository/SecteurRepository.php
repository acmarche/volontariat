<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Doctrine\OrmCrudTrait;
use AcMarche\Volontariat\Entity\Secteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Secteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Secteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Secteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecteurRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Secteur::class);
    }

    public function findAllOrdered(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    /**
     * @return Secteur[]
     */
    public function getForSearch(): array
    {
        $qb = $this->createQueryBuilder('secteur');

        $qb->andWhere('secteur.display = 1');
        $qb->orderBy('secteur.name');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $types = [];

        foreach ($results as $type) {
            $types[$type->getName()] = $type->getId();
        }

        return $types;
    }

    public function secteursActifs(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('secteur');
        $qb->andWhere('secteur.display = 1');

        $qb->orderBy('secteur.name');

        return $qb;
    }
}
