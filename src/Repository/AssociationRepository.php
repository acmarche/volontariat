<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Doctrine\OrmCrudTrait;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    /**
     * @return Association[]
     */
    public function findAll(): array
    {
        return $this->findBy(array(), array('nom' => 'ASC'));
    }

    /**
     * @return Association[]
     */
    public function search(array $args): array
    {
        $nom = $args['nom'] ?? null;
        $secteur = $args['secteur'] ?? null;
        $secteurs = $args['secteurs'] ?? null;
        $user = $args['user'] ?? null;
        $valider = $args['valider'] ?? true;

        $qb = $this->createQBl();

        if ($nom) {
            $qb->andwhere(
                'association.email LIKE :mot OR association.nom LIKE :mot OR association.description LIKE :mot '
            )
                ->setParameter('mot', '%'.$nom.'%');
        }

        if ($secteur) {
            $qb->andwhere('secteurs = :secteur ')
                ->setParameter('secteur', $secteur);
        }

        if (is_array($secteurs)) {
            $qb->andwhere('secteurs IN ARRAY :secteurs ')
                ->setParameter('secteurs', $secteurs);
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

        return $qb->addOrderBy('association.nom', 'ASC')->getQuery()->getResult();
    }

    /**
     * @return Association[]
     */
    public function getRecent(int $limit = 8): array
    {
        return $this->createQBl()
            ->setMaxResults($limit)
            ->addOrderBy('RAND()')
            ->getQuery()
            ->getResult();
    }

    public function getAllEmail(): array
    {
        $results = $this->createQBl()
            ->andWhere("association.mailing = 0")
            ->getQuery()
            ->getResult();

        $npo_emails = array();
        foreach ($results as $association) {
            if ($association->getEmail()) {
                $npo_emails[] = $association->getEmail();
            }
        }

        return $npo_emails;
    }

    /**
     * @return Association[]
     */
    public function findAssociationBySecteur($secteurs): array
    {
        $qb = $this->createQBl()
            ->where(':platform MEMBER OF association.secteurs')
            ->setParameters(array('platform' => $secteurs));

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Association[]
     */
    public function getAssociationsByUser(User $user, bool $valider = false): array
    {
        $qb = $this->createQBl()
            ->where('association.user = :user')
            ->setParameter('user', $user)
            ->where('association.valider = :valider')
            ->setParameter('valider', $valider);

        return $qb->getQuery()->getResult();
    }

    private function createQBl(): QueryBuilder
    {
        return $this->createQueryBuilder('association')
            ->leftJoin('association.secteurs', 'secteurs', 'WITH')
            ->leftJoin('association.besoins', 'besoins', 'WITH')
            ->leftJoin('association.user', 'user', 'WITH')
            ->addSelect('secteurs', 'besoins', 'user');
    }

}
