<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Doctrine\OrmCrudTrait;
use AcMarche\Volontariat\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(
        PasswordAuthenticatedUserInterface $passwordAuthenticatedUser,
        string $newHashedPassword
    ): void {
        if (!$passwordAuthenticatedUser instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', $passwordAuthenticatedUser::class)
            );
        }

        $passwordAuthenticatedUser->password = $newHashedPassword;

        $this->flush();
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findOneByEmailAndSkip($email, User $user): ?User
    {
        return $this
            ->createQueryBuilder('user')
            ->andWhere('user.email = :email')
            ->setParameter('email', $email)
            ->andWhere('user.id != :id')
            ->setParameter('id', $user->getId())
            ->getQuery()->getOneOrNullResult();
    }

    public function qbqForList(): QueryBuilder
    {
        return $this->createQueryBuilder('user')->addOrderBy('user.name');
    }

    /**
     * @param string $name
     * @return array<int, User>
     */
    public function findByName(string $name): array
    {
        return $this
            ->createQueryBuilder('user')
            ->andWhere('user.email LIKE :name OR user.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery()->getResult();
    }

    public function findOneByTokenValue(string $value): ?User
    {
        return $this->findOneBy(['tokenValue' => $value]);
    }
}
