<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Entity\Secteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Secteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Secteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Secteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Secteur::class);
    }


    public function insert(Secteur $secteur)
    {
        $this->_em->persist($secteur);
        $this->save();
    }

    public function save()
    {
        $this->_em->flush();
    }

    public function remove(Secteur $secteur)
    {
        $this->_em->remove($secteur);
        $this->save();
    }

    public function findAll()
    {
        return $this->findBy(array(), array('name' => 'ASC'));
    }

    /**
     * @return Secteur[]
     */
    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('secteur');

        $qb->andWhere('secteur.display = 1');
        $qb->orderBy('secteur.name');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $types = array();

        foreach ($results as $type) {
            $types[$type->getName()] = $type->getId();
        }

        return $types;
    }


    public function secteursActifs()
    {
        $qb = $this->createQueryBuilder('secteur');
        $qb->andWhere('secteur.display = 1');

        $qb->orderBy('secteur.name');

        return $qb;
    }
}
