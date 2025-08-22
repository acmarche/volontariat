<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Doctrine\OrmCrudTrait;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Entity\Volontaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Association|null find($id, $lockMode = null, $lockVersion = null)
 * @method Association|null findOneBy(array $criteria, array $orderBy = null)
 * @method Association[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Association>
 */
class AssociationRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Association::class);
    }

    /**
     * @return Association[]
     */
    public function findAll(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
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

        $queryBuilder = $this->createQBl();

        if ($nom) {
            $queryBuilder
                ->andWhere(
                    'association.email LIKE :mot OR association.name LIKE :mot OR association.description LIKE :mot ',
                )
                ->setParameter('mot', '%'.$nom.'%');
        }

        if ($secteur) {
            $queryBuilder
                ->andWhere('secteurs = :secteur ')
                ->setParameter('secteur', $secteur);
        }

        if (is_array($secteurs)) {
            $queryBuilder
                ->andWhere('secteurs IN ARRAY :secteurs ')
                ->setParameter('secteurs', $secteurs);
        }

        if ($user) {
            $queryBuilder
                ->andWhere('user = :user')
                ->setParameter('user', $user);
        }

        if (false === $valider) {
            $queryBuilder
                ->andWhere('association.valider = :valider')
                ->setParameter('valider', false);
        } elseif (2 != $valider) {
            $queryBuilder
                ->andWhere('association.valider = :valider')
                ->setParameter('valider', true);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Association[]
     */
    public function searchFront(string $keyword): array
    {
        return $this
            ->createQBl()
            ->andWhere('association.notification_message_association = 1')
            ->andWhere(
                'association.email LIKE :mot OR association.name LIKE :mot OR association.description LIKE :mot ',
            )
            ->setParameter('mot', '%'.$keyword.'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Association[]
     */
    public function findAcceptMessage(): array
    {
        return $this
            ->createQBl()
            ->andWhere('association.notification_message_association = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Association[]
     */
    public function getRecent(int $limit = 9): array
    {
        return $this
            ->createQBl()
            ->setMaxResults($limit)
            ->addOrderBy('RAND()')
            ->getQuery()
            ->getResult();
    }

    public function getAllEmail(): array
    {
        $results = $this
            ->createQBl()
            ->andWhere('association.mailing = 0')
            ->getQuery()
            ->getResult();

        $npo_emails = [];
        foreach ($results as $result) {
            if ($result->getEmail()) {
                $npo_emails[] = $result->email;
            }
        }

        return $npo_emails;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findAssociationByUser(UserInterface $user): ?Association
    {
        return $this
            ->createQBl()
            ->andWhere('association.user = :user')
            ->setParameter('user', $user)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return array|Association[]
     */
    public function findAssociationsBySecteur(Secteur $secteur): array
    {
        return $this
            ->createQBl()
            ->andWhere(':secteurId MEMBER OF association.secteurs')
            ->setParameter('secteurId', $secteur->getId())
            ->getQuery()->getResult();
    }

    /**
     * @return array|Association[]
     */
    public function getAssociationsWithSameSecteur(Volontaire $volontaire): array
    {
        $associations = [[]];
        $secteurs = $volontaire->secteurs;
        foreach ($secteurs as $secteur) {
            if ($this->findAssociationsBySecteur($secteur) !== []) {
                $associations[] = $this->findAssociationsBySecteur($secteur);
            }
        }

        $t = array_merge(...$associations);

        foreach ($t as $association) {
            $t[$association->getId()] = $association;
        }

        return $t;
    }

    private function createQBl(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('association')
            ->leftJoin('association.secteurs', 'secteurs', 'WITH')
            ->leftJoin('association.besoins', 'besoins', 'WITH')
            ->leftJoin('association.user', 'user', 'WITH')
            ->addSelect('secteurs', 'besoins', 'user')
            ->addOrderBy('association.name', 'ASC');
    }

    /**
     * Use MapEntity url
     */
    public function findOneByUuid(string $uuid): ?Association
    {
        return $this
            ->createQueryBuilder('association')
            ->andWhere('association.uuid = :uuid')
            ->setParameter('uuid', $uuid, ParameterType::STRING)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
