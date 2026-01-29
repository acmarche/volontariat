<?php

namespace AcMarche\Volontariat\Entity\Security;

use AcMarche\Volontariat\Entity\PasswordAuthenticableTrait;
use Stringable;
use Doctrine\DBAL\Types\Types;
use DateTimeInterface;
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
    use PasswordAuthenticableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    public string $email;

    #[ORM\Column(type: Types::JSON, nullable: false)]
    public array $roles = [];

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    public ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    public ?string $surname;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    public ?bool $accord = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTimeInterface $accord_date = null;

    public ?string $city = null;

    public function __toString(): string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function addRole(string $role): void
    {
        if (!in_array(strtoupper($role), $this->getRoles(), true)) {
            $this->roles[] = strtoupper($role);
        }
    }

    public function removeRole(string $role): void
    {
        if (in_array(strtoupper($role), $this->getRoles(), true)) {
            $this->roles = array_filter($this->roles, function ($existingRole) use ($role) {
                return strtoupper($existingRole) !== strtoupper($role);
            });
            // Re-index the array to avoid gaps
            $this->roles = array_values($this->roles);
        }
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

}
