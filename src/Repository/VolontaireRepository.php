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
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Volontaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Volontaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Volontaire[]    findAll()
 * @method Volontaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Volontaire>
 */
class VolontaireRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Volontaire::class);
    }

    public function upgradePassword(
        PasswordAuthenticatedUserInterface $passwordAuthenticatedUser,
        string $newHashedPassword
    ): void {
        if (!$passwordAuthenticatedUser instanceof Volontaire) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', $passwordAuthenticatedUser::class)
            );
        }

        $passwordAuthenticatedUser->password = $newHashedPassword;
        $this->flush();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findAssociationByUser(UserInterface $user): ?Volontaire
    {
        return $this
            ->createQBl()
            ->andWhere('volontaire.user = :user')
            ->setParameter('user', $user)
            ->getQuery()->getOneOrNullResult();
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
        $localite = $args['city'] ?? null;
        $createdAt = $args['createdAt'] ?? null;

        $queryBuilder = $this->createQbl();

        if ($nom) {
            $queryBuilder
                ->andWhere('volontaire.email LIKE :mot OR volontaire.name LIKE :mot OR volontaire.surname LIKE :mot ')
                ->setParameter('mot', '%'.$nom.'%');
        }

        if ($localite) {
            $queryBuilder
                ->andWhere('volontaire.city LIKE :loca OR volontaire.postalCode LIKE :loca ')
                ->setParameter('loca', '%'.$localite.'%');
        }

        if ($createdAt instanceof \DateTimeInterface) {
            $queryBuilder
                ->andWhere('volontaire.createdAt >= :date ')
                ->setParameter('date', $createdAt->format('Y-m').'-01');
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

        return $queryBuilder->addOrderBy('volontaire.name', 'ASC')->getQuery()->getResult();
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

    public function findOneByEmail(string $email): ?Volontaire
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findOneByTokenValue(string $value): ?Volontaire
    {
        return $this->findOneBy(['tokenValue' => $value]);
    }

    /**
     * @return Volontaire[]
     */
    public function findVolontairesWantBeNotified(): array
    {
        return $this
            ->createQbl()
            ->andWhere('volontaire.notification_message_association = :notification')
            ->andWhere('volontaire.inactif = :inactif')
            ->setParameter('notification', true)
            ->setParameter('inactif', false)
            ->getQuery()->getResult();
    }

    public function createQbl(): QueryBuilder
    {
        return $this
            ->createQueryBuilder('volontaire')
            ->leftJoin('volontaire.association', 'association', 'WITH')
            ->leftJoin('volontaire.secteurs', 'secteurs', 'WITH')
            ->leftJoin('volontaire.vehicules', 'vehicules', 'WITH')
            ->addSelect('secteurs', 'vehicules', 'association');
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
            ->andWhere('volontaire.inactif = :inactif')
            ->setParameter('notification', true)
            ->setParameter('inactif', false)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int,Volontaire>
     */
    public function findActif(): array
    {
        return $this
            ->createQueryBuilder('volontaire')
            ->andWhere('volontaire.inactif = :inactif')
            ->setParameter('inactif', false)
            ->getQuery()
            ->getResult();
    }
}
