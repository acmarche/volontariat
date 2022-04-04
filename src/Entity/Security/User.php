<?php

namespace AcMarche\Volontariat\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements UserInterface, LegacyPasswordAuthenticatedUserInterface, Stringable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @var iterable $roles
     * @ORM\Column(type="array", nullable=true)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $prenom;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $accord;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $accord_date;

    /**
     * @var Token|null $token
     * @ORM\OneToOne(targetEntity="AcMarche\Volontariat\Entity\Security\Token", mappedBy="user", cascade={"remove"})
     */
    private $token;

    /**
     * Random string sent to the user email address in order to verify it.
     * @ORM\Column(name="confirmation_token",type="string", length=180, unique=true, nullable=true)
     * @var string|null
     */
    private $confirmationToken;

    /**
     * @var string|null
     *
     */
    private $plain_password;
    /**
     * @var int
     */
    private $countAssociations= 0 ;
    /**
     * @var int
     */
    private $countVolontaires = 0;

    public function setCountVolontaires(int $count)
    {
        $this->countVolontaires = $count;
    }

    public function setCountAssociations(int $count)
    {
        $this->countAssociations = $count;
    }

    /**
     * @return int
     */
    public function getCountAssociations(): int
    {
        return $this->countAssociations;
    }

    /**
     * @return int
     */
    public function getCountVolontaires(): int
    {
        return $this->countVolontaires;
    }

    public function __toString()
    {
        return (string)$this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
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

    public function hasRole($role)
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
        return (string)$this->password;
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
    public function eraseCredentials()
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

    /**
     * @return null|string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plain_password;
    }

    public function setPlainPassword(?string $plain_password): self
    {
        $this->plain_password = $plain_password;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAccord(): ?bool
    {
        return $this->accord;
    }

    /**
     * @param bool|null $accord
     */
    public function setAccord(?bool $accord): void
    {
        $this->accord = $accord;
    }

    /**
     * @return \DateTime|null
     */
    public function getAccordDate(): ?\DateTime
    {
        return $this->accord_date;
    }

    /**
     * @param \DateTime|null $accord_date
     */
    public function setAccordDate(?\DateTime $accord_date): void
    {
        $this->accord_date = $accord_date;
    }

    /**
     * @return Token|null
     */
    public function getToken(): ?Token
    {
        return $this->token;
    }

    /**
     * @param Token|null $token
     */
    public function setToken(?Token $token): void
    {
        $this->token = $token;
    }

    /**
     * @return null|string
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param null|string $confirmationToken
     */
    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

}
