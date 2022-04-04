<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 8/02/17
 * Time: 12:36
 */

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Repository\VehiculeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
#[ORM\Table(name: 'vehicule')]
class Vehicule implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    protected string $nom;
    /**
     * @var Volontaire[]|iterable
     */
    #[ORM\ManyToMany(targetEntity: Volontaire::class, mappedBy: 'vehicules')]
    private Collection $volontaires;

    public function __toString(): string
    {
        return $this->getNom();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->volontaires = new ArrayCollection();
    }

    /**
     * Get id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     */
    public function setNom($nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Add volontaire
     *
     *
     */
    public function addVolontaire(Volontaire $volontaire): static
    {
        $this->volontaires[] = $volontaire;

        return $this;
    }

    /**
     * Remove volontaire
     */
    public function removeVolontaire(Volontaire $volontaire): void
    {
        $this->volontaires->removeElement($volontaire);
    }

    /**
     * Get volontaires
     */
    public function getVolontaires(): iterable
    {
        return $this->volontaires;
    }
}
