<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Doctrine\OrmCrudTrait;
use Doctrine\ORM\NonUniqueResultException;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Entity\Volontaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Association|null find($id, $lockMode = null, $lockVersion = null)
 * @method Association|null findOneBy(array $criteria, array $orderBy = null)
 * @method Association[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssociationRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Association::class);
    }

    public function insert(Association $association): void
    {
        $this->_em->persist($association);
        $this->save();
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function remove(Association $association): void
    {
        $this->_em->remove($association);
        $this->save();
    }

    /**
     * @return Association[]
     */
    public function findAll(): array
    {
        return $this->findBy(array(), array('nom' => 'ASC'));
    }

    /**
     * @param $args
     * @return Association[]
     * @throws NonUniqueResultException
     */
    public function search($args)
    {
        $nom = $args['nom'] ?? null;
        $secteur = $args['secteur'] ?? null;
        $secteurs = $args['secteurs'] ?? null;
        $user = $args['user'] ?? null;
        $one = $args['one'] ?? null;
        $valider = $args['valider'] ?? true;

        $qb = $this->createQueryBuilder('association');
        $qb->leftJoin('association.secteurs', 'secteurs', 'WITH');
        $qb->leftJoin('association.besoins', 'besoins', 'WITH');
        $qb->leftJoin('association.user', 'user', 'WITH');
        $qb->addSelect('secteurs', 'besoins', 'user');

        if ($nom) {
            $qb->andwhere(
                'association.email LIKE :mot OR association.nom LIKE :mot OR association.description LIKE :mot '
            )
                ->setParameter('mot', '%' . $nom . '%');
        }

        if ($secteur) {
            $qb->andwhere('secteurs = :secteur ')
                ->setParameter('secteur', $secteur);
        }

        if (is_array($secteurs)) {
            $secteursIds = implode(",", $secteurs);
            $qb->andwhere('secteurs = :secteurs ')
                ->setParameter('secteurs', '(' . $secteursIds . ')');
        }

        if ($user) {
            $qb->andwhere('user = :user')
                ->setParameter('user', $user);
        }

        if ($valider === false) {
            $qb->andwhere('association.valider = :valider')
                ->setParameter('valider', false);
        } elseif ($valider != 2) {
            $qb->andwhere('association.valider = :valider')
                ->setParameter('valider', true);
        }

        $qb->addOrderBy('association.nom', 'ASC');

        $query = $qb->getQuery();

        if ($one) {
            return $query->getOneOrNullResult();
        }

        return $query->getResult();
    }

    /**
     * @return Association[]
     */
    public function getForAssociation(): array
    {
        $qb = $this->createQueryBuilder('association');

        $qb->andwhere('association.user IS NULL');

        $qb->orderBy('association.nom');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $types = array();

        foreach ($results as $type) {
            $types[$type->getNom()] = $type->getId();
        }

        return $types;
    }

    /**
     * @param int $limit
     * @return Association[]
     */
    public function getRecent($limit = 8): array
    {
        $qb = $this->createQueryBuilder('association');
        // $qb->leftJoin('a.secteurs', 'secteurs', 'WITH');
        // $qb->addSelect('secteurs');

        $qb->setMaxResults($limit);
        $qb->addOrderBy('RAND()');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getAllEmail(): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->andWhere("a.mailing = 0");

        $query = $qb->getQuery();
        $results = $query->getResult();

        $npo_emails = array();
        foreach ($results as $association) {
            if ($association->getEmail()) {
                $npo_emails[] = $association->getEmail();
            }
        }

        return $npo_emails;
    }


    public function findAssociationBySecteur($secteurs)
    {
        $qb = $this->createQueryBuilder("association")
            ->where(':platform MEMBER OF association.secteurs')
            ->setParameters(array('platform' => $secteurs));

        return $qb->getQuery()->getResult();
    }
}
