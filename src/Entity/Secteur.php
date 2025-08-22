<?php

namespace AcMarche\Volontariat\Entity;

use Stringable;
use Doctrine\DBAL\Types\Types;
use AcMarche\Volontariat\Repository\SecteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SecteurRepository::class)]
#[ORM\Table(name: 'secteur')]
class Secteur implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    protected ?string $name;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    protected ?string $description;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => 1])]
    private bool $display = true;

    /**
     * @var Association[]|iterable
     */
    #[ORM\ManyToMany(targetEntity: Association::class, mappedBy: 'secteurs')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    protected Collection $associations;

    /**
     * @var Volontaire[]|iterable
     */
    #[ORM\ManyToMany(targetEntity: Volontaire::class, mappedBy: 'secteurs')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    protected Collection $volontaires;

    public function __toString(): string
    {
        return $this->getName();
    }

    public function toStringWithDescription(): string
    {
        $txt = $this->getName();
        if ($this->getDescription()) {
            $txt .= ' ('.$this->getDescription().')';
        }

        return $txt;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->associations = new ArrayCollection();
        $this->volontaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName(?string $name): static
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
     * Set description.
     */
    public function setDescription(?string $description = null): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Add association.
     */
    public function addAssociation(Association $association): static
    {
        $this->associations[] = $association;

        return $this;
    }

    /**
     * Remove association.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeAssociation(Association $association): bool
    {
        return $this->associations->removeElement($association);
    }

    /**
     * Get associations.
     * @return Collection<int, Association>
     */
    public function getAssociations(): iterable
    {
        return $this->associations;
    }

    /**
     * Add volontaire.
     */
    public function addVolontaire(Volontaire $volontaire): static
    {
        $this->volontaires[] = $volontaire;

        return $this;
    }

    /**
     * Remove volontaire.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeVolontaire(Volontaire $volontaire): bool
    {
        return $this->volontaires->removeElement($volontaire);
    }

    /**
     * Get volontaires.
     * @return Collection<int, Volontaire>
     */
    public function getVolontaires(): iterable
    {
        return $this->volontaires;
    }

    public function getDisplay(): bool
    {
        return $this->display;
    }

    public function setDisplay(bool $display): self
    {
        $this->display = $display;

        return $this;
    }
}
