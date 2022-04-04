<?php

namespace AcMarche\Volontariat\Entity\Security;

use AcMarche\Volontariat\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
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
    private $id;
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;
    #[ORM\Column(type: 'array', nullable: true)]
    private iterable $roles = [];
    /**
     * The hashed password
     */
    #[ORM\Column(type: 'string')]
    private string $password;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $salt;
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $nom;
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $prenom;
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $accord = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $accord_date = null;
    #[ORM\OneToOne(targetEntity: Token::class, mappedBy: 'user', cascade: ['remove'])]
    private ?Token $token = null;
    /**
     * Random string sent to the user email address in order to verify it.
     */
    #[ORM\Column(name: 'confirmation_token', type: 'string', length: 180, unique: true, nullable: true)]
    private ?string $confirmationToken = null;
    private ?string $plain_password = null;
    private int $countAssociations = 0;
    private int $countVolontaires = 0;

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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function hasRole($role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return (string)$this->salt;  // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plain_password;
    }

    public function setPlainPassword(?string $plain_password): self
    {
        $this->plain_password = $plain_password;

        return $this;
    }

    public function getAccord(): ?bool
    {
        return $this->accord;
    }

    public function setAccord(?bool $accord): void
    {
        $this->accord = $accord;
    }

    /**
     * @return DateTime|DateTimeImmutable|null
     */
    public function getAccordDate(): ?DateTimeInterface
    {
        return $this->accord_date;
    }

    /**
     * @param DateTime|null $accord_date
     */
    public function setAccordDate(?DateTimeInterface $accord_date): void
    {
        $this->accord_date = $accord_date;
    }

    public function getToken(): ?Token
    {
        return $this->token;
    }

    public function setToken(?Token $token): void
    {
        $this->token = $token;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }
}
