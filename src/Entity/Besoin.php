<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Repository\BesoinRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Besoin
 *
 *
 */
#[ORM\Table(name: 'besoin')]
#[ORM\Entity(repositoryClass: BesoinRepository::class)]
class Besoin implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: 'string', nullable: false)]
    #[ORM\OrderBy(['intitule' => 'ASC'])]
    #[Assert\NotBlank]
    protected ?string $name;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(DateTime::class)]
    protected ?DateTimeInterface $date_begin;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Type(DateTime::class)]
    protected ?DateTimeInterface $date_end;
    /**
     * Description
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    protected ?string $requirement;
    /**
     * quand
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    protected ?string $period;
    /**
     * lieu
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    protected ?string $place;

    #[ORM\ManyToOne(targetEntity: Association::class, inversedBy: 'besoins')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Association $association;

    public function __toString(): string
    {
        return $this->getName();
    }
    /**
     * STOP
     */
    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set dateBegin.
     *
     * @param DateTime $dateBegin
     */
    public function setDateBegin(DateTimeInterface $dateBegin): static
    {
        $this->date_begin = $dateBegin;

        return $this;
    }

    /**
     * Get dateBegin.
     *
     * @return DateTime|DateTimeImmutable|null
     */
    public function getDateBegin(): ?\DateTimeInterface
    {
        return $this->date_begin;
    }

    /**
     * Set dateEnd.
     *
     *
     */
    public function setDateEnd(DateTime|DateTimeImmutable $dateEnd = null): static
    {
        $this->date_end = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd.
     *
     * @return DateTime|DateTimeImmutable|null
     */
    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->date_end;
    }

    /**
     * Set requirement.
     *
     * @param string $requirement
     */
    public function setRequirement($requirement): static
    {
        $this->requirement = $requirement;

        return $this;
    }

    /**
     * Get requirement.
     */
    public function getRequirement(): ?string
    {
        return $this->requirement;
    }

    /**
     * Set period.
     *
     * @param string $period
     */
    public function setPeriod($period): static
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period.
     */
    public function getPeriod(): ?string
    {
        return $this->period;
    }

    /**
     * Set place.
     *
     * @param string $place
     */
    public function setPlace($place): static
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place.
     */
    public function getPlace(): ?string
    {
        return $this->place;
    }

    /**
     * Set association.
     *
     *
     */
    public function setAssociation(Association $association): static
    {
        $this->association = $association;

        return $this;
    }

    /**
     * Get association.
     */
    public function getAssociation(): ?Association
    {
        return $this->association;
    }
}
