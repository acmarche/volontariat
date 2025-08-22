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
 * @extends ServiceEntityRepository<Secteur>
 */
class SecteurRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Secteur::class);
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
        $queryBuilder = $this->createQueryBuilder('secteur');

        $queryBuilder->andWhere('secteur.display = 1');
        $queryBuilder->orderBy('secteur.name');

        $query = $queryBuilder->getQuery();

        $results = $query->getResult();
        $types = [];

        foreach ($results as $result) {
            $types[$result->getName()] = $result->getId();
        }

        return $types;
    }

    public function secteursActifs(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('secteur');
        $queryBuilder->andWhere('secteur.display = 1');

        $queryBuilder->orderBy('secteur.name');

        return $queryBuilder;
    }
}
