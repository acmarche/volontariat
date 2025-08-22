<?php

namespace AcMarche\Volontariat\Entity\Security;

use Stringable;
use Doctrine\DBAL\Types\Types;
use DateTimeInterface;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordHasherAwareInterface, LegacyPasswordAuthenticatedUserInterface, Stringable, TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    public string $email;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    public iterable $roles = [];

    #[ORM\Column(type: Types::STRING)]
    public string $password;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $salt = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    public ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    public ?string $surname;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    public ?bool $accord = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTimeInterface $accord_date = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Token::class, cascade: ['remove'])]
    public ?Token $token = null;

    // register voluntary
    public ?string $plainPassword = null;

    public ?string $city = null;

    public ?Volontaire $volontaire = null;

    public ?Association $association = null;

    public function __toString(): string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public static function createFromAssociation(Association $association): self
    {
        $user = new self();
        $user->name = $association->name;
        $user->email = $association->email;

        return $user;
    }

    public function getSalt(): ?string
    {
        return (string)$this->salt;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function hasRole($role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPasswordHasherName(): ?string
    {
        return 'cap_hasher';
    }
}
