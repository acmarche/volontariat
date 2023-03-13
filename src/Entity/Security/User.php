<?php

namespace AcMarche\Volontariat\Entity\Security;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, LegacyPasswordAuthenticatedUserInterface, Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    public string $email;
    #[ORM\Column(type: 'array', nullable: true)]
    public iterable $roles = [];
    /**
     * The hashed password
     */
    #[ORM\Column(type: 'string')]
    public string $password;
    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $salt;
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    public ?string $nom;
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    public ?string $prenom;
    #[ORM\Column(type: 'boolean', nullable: true)]
    public ?bool $accord = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTimeInterface $accord_date = null;
    #[ORM\OneToOne(targetEntity: Token::class, mappedBy: 'user', cascade: ['remove'])]
    public ?Token $token = null;
    /**
     * Random string sent to the user email address in order to verify it.
     */
    #[ORM\Column(name: 'confirmation_token', type: 'string', length: 180, unique: true, nullable: true)]
    public ?string $confirmationToken = null;
    public ?string $plain_password = null;
    public int $countAssociations = 0;
    public int $countVolontaires = 0;

    //register voluntary
    public ?string $city = null;
    public ?string $name = null;
    public ?string $surname = null;

    public function setCountVolontaires(int $count): void
    {
        $this->countVolontaires = $count;
    }

    public function setCountAssociations(int $count): void
    {
        $this->countAssociations = $count;
    }

    public function getCountAssociations(): int
    {
        return $this->countAssociations;
    }

    public function getCountVolontaires(): int
    {
        return $this->countVolontaires;
    }

    public function __toString()
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
        return $this->salt;
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

    public function eraseCredentials()
    {

    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
