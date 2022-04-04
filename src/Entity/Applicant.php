<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Repository\ApplicantRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Volontaire
 *
 *
 */
#[ORM\Table(name: 'applicant')]
#[ORM\Entity(repositoryClass: ApplicantRepository::class)]
class Applicant implements TimestampableInterface, Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    protected string $name;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotNull]
    protected ?string $surname;

    #[ORM\Column(type: 'string', nullable: false)]
    protected ?string $city;

    #[ORM\Column(name: 'email', nullable: true)]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    protected ?string $email;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $phone;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $notes;

    public function __toString(): string
    {
        return $this->getSurname().' '.$this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }
}
