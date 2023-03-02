<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Doctrine\OrmCrudTrait;
use Doctrine\ORM\NonUniqueResultException;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Entity\Volontaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Volontaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Volontaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Volontaire[]    findAll()
 * @method Volontaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VolontaireRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Volontaire::class);
    }

    /**
     * @param $args
     * @return Volontaire[]|Volontaire
     * @throws NonUniqueResultException
     */
    public function search($args): array|Volontaire
    {
        $nom = $args['nom'] ?? null;
        $secteur = $args['secteur'] ?? null;
        $secteurs = $args['secteurs'] ?? null;
        $vehicule = $args['vehicule'] ?? null;
        $user = $args['user'] ?? null;
        $localite = $args['city'] ?? null;
        $one = $args['one'] ?? null;
        $valider = $args['valider'] ?? null;
        $createdAt = $args['createdAt'] ?? null;

        $qb = $this->createQueryBuilder('volontaire');
        $qb->leftJoin('volontaire.association', 'association', 'WITH');
        $qb->leftJoin('volontaire.secteurs', 'secteurs', 'WITH');
        $qb->leftJoin('volontaire.user', 'user', 'WITH');
        $qb->leftJoin('volontaire.vehicules', 'vehicules', 'WITH');
        $qb->addSelect('secteurs', 'vehicules', 'user', 'association');

        if ($nom) {
            $qb->andwhere('volontaire.email LIKE :mot OR volontaire.name LIKE :mot OR volontaire.surname LIKE :mot ')
                ->setParameter('mot', '%'.$nom.'%');
        }

        if ($localite) {
            $qb->andwhere('volontaire.city LIKE :loca ')
                ->setParameter('loca', '%'.$localite.'%');
        }

         if ($createdAt) {
            $qb->andwhere('volontaire.createdAt >= :date ')
                ->setParameter('date', $createdAt);
        }

        if ($secteur) {
            $qb->andwhere('secteurs = :secteur ')
                ->setParameter('secteur', $secteur);
        }

        if (is_array($secteurs) && $secteurs !== []) {
            $secteursIds = implode(",", $secteurs);

            $qb->andwhere("secteurs IN ($secteursIds) ");
        }

        if ($vehicule) {
            $qb->andwhere('vehicules = :vehicule')
                ->setParameter('vehicule', $vehicule);
        }

        if ($valider === false) {
            $qb->andwhere('volontaire.valider = :valider')
                ->setParameter('valider', false);
        } elseif ($valider != 2) {
            $qb->andwhere('volontaire.valider = :valider')
                ->setParameter('valider', true);
        }

        if ($user) {
            $qb->andwhere('user = :user')
                ->setParameter('user', $user);
        }

        $qb->addOrderBy('volontaire.name', 'ASC');

        $query = $qb->getQuery();

        if ($one) {
            return $query->getOneOrNullResult();
        }

        return $query->getResult();
    }

    /**
     * @return Volontaire[]
     */
    public function getForAssociation(): array
    {
        $qb = $this->createQueryBuilder('volontaire');
        $qb->andwhere('volontaire.user IS NULL');

        $qb->orderBy('volontaire.name');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $types = array();

        foreach ($results as $type) {
            $types[$type->getName()] = $type->getId();
        }

        return $types;
    }

    /**
     * @param int $max
     * @return Volontaire[]
     */
    public function getRecent($max = 8): array
    {
        $qb = $this->createQueryBuilder('volontaire');

        $qb->setMaxResults($max);
        $qb->addOrderBy('RAND()');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getLocalitesForSearch(): array
    {
        $qb = $this->createQueryBuilder('volontaire');

        $qb->orderBy('volontaire.city');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $cities = array();

        foreach ($results as $type) {
            $city = strtoupper($type->getCity());
            if (!in_array($city, $cities)) {
                $cities[$city] = $city;
            }
        }

        return $cities;
    }

    public function findVolontaireBySecteur($secteurs)
    {
        $qb = $this->createQueryBuilder("volontaire")
            ->where(':platform MEMBER OF volontaire.secteurs')
            ->setParameters(array('platform' => $secteurs));

        return $qb->getQuery()->getResult();
    }
}
