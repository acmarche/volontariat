<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Doctrine\OrmCrudTrait;
use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Entity\Volontaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Volontaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Volontaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Volontaire[]    findAll()
 * @method Volontaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Volontaire>
 */
class VolontaireRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Volontaire::class);
    }

    /**
     * @return Volontaire[]
     */
    public function search(array $args): array
    {
        $nom = $args['nom'] ?? null;
        $secteur = $args['secteur'] ?? null;
        $secteurs = $args['secteurs'] ?? null;
        $vehicule = $args['vehicule'] ?? null;
        $user = $args['user'] ?? null;
        $localite = $args['city'] ?? null;
        $valider = $args['valider'] ?? null;
        $createdAt = $args['createdAt'] ?? null;

        $queryBuilder = $this->createQbl();

        if ($nom) {
            $queryBuilder
                ->andWhere('volontaire.email LIKE :mot OR volontaire.name LIKE :mot OR volontaire.surname LIKE :mot ')
                ->setParameter('mot', '%'.$nom.'%');
        }

        if ($localite) {
            $queryBuilder
                ->andWhere('volontaire.city LIKE :loca ')
                ->setParameter('loca', '%'.$localite.'%');
        }

        if ($createdAt) {
            $queryBuilder
                ->andWhere('volontaire.createdAt >= :date ')
                ->setParameter('date', $createdAt);
        }

        if ($secteur) {
            $queryBuilder
                ->andWhere('secteurs = :secteur ')
                ->setParameter('secteur', $secteur);
        }

        if (is_array($secteurs) && [] !== $secteurs) {
            $queryBuilder
                ->andWhere('secteurs IN :secteurs')
                ->setParameter('secteurs', $secteur);
        }

        if ($vehicule) {
            $queryBuilder
                ->andWhere('vehicules = :vehicule')
                ->setParameter('vehicule', $vehicule);
        }

        if (false === $valider) {
            $queryBuilder
                ->andWhere('volontaire.valider = :valider')
                ->setParameter('valider', false);
        } elseif (2 != $valider) {
            $queryBuilder
                ->andWhere('volontaire.valider = :valider')
                ->setParameter('valider', true);
        }

        if ($user) {
            $queryBuilder
                ->andWhere('user = :user')
                ->setParameter('user', $user);
        }

        $queryBuilder->addOrderBy('volontaire.name', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Volontaire[]
     */
    public function getRecent(int $max = 8): array
    {
        return $this
            ->createQbl()
            ->setMaxResults($max)
            ->addOrderBy('RAND()')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return string[]
     */
    public function getLocalitesForSearch(): array
    {
        $results = $this
            ->createQbl()
            ->orderBy('volontaire.city')
            ->getQuery()->getResult();

        $cities = [];

        foreach ($results as $result) {
            $city = strtoupper($result->city);
            if (!in_array($city, $cities)) {
                $cities[$city] = $city;
            }
        }

        return $cities;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findVolontaireByUser(UserInterface $user): ?Volontaire
    {
        return $this
            ->createQbl()
            ->andWhere('volontaire.user = :user')
            ->setParameter('user', $user)
            ->orderBy('volontaire.city')
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Volontaire[]
     */
    public function findVolontairesWantBeNotified(): array
    {
        return $this
            ->createQbl()
            ->andWhere('volontaire.notification_message_association = :notification')
            ->andWhere('volontaire.valider = :valider')
            ->andWhere('volontaire.inactif = :inactif')
            ->setParameter('notification', true)
            ->setParameter('valider', true)
            ->setParameter('inactif', false)
            ->getQuery()->getResult();
    }

    public function createQbl(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('volontaire')
            ->leftJoin('volontaire.association', 'association', 'WITH')
            ->leftJoin('volontaire.secteurs', 'secteurs', 'WITH')
            ->leftJoin('volontaire.user', 'user', 'WITH')
            ->leftJoin('volontaire.vehicules', 'vehicules', 'WITH')
            ->addSelect('secteurs', 'vehicules', 'user', 'association');
    }

    /**
     * Use MapEntity url
     */
    public function findOneByUuid(string $uuid): ?Volontaire
    {
        return $this
            ->createQueryBuilder('volontaire')
            ->andWhere('volontaire.uuid = :uuid')
            ->setParameter('uuid', $uuid, ParameterType::STRING)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Secteur $secteur
     * @return array<int,Volontaire>
     */
    public function findVolontaireBySecteur(Secteur $secteur): array
    {
        return $this
            ->createQueryBuilder('volontaire')
            ->andWhere(':id MEMBER OF volontaire.secteurs')
            ->setParameter('id', $secteur->getId(), ParameterType::INTEGER)
            ->andWhere('volontaire.notification_message_association = :notification')
        //    ->andWhere('volontaire.valider = :valider')
       //     ->andWhere('volontaire.inactif = :inactif')
            ->setParameter('notification', true)
          //  ->setParameter('valider', true)
          //  ->setParameter('inactif', false)
            ->getQuery()
            ->getResult();
    }
}
