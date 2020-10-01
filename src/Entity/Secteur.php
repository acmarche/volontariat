<?php

namespace AcMarche\Volontariat\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Iterable_;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\SecteurRepository")
 * @ORM\Table(name="secteur")
 *
 */
class Secteur
{
    /**
     * @var integer|null $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    protected $id;

    /**
     * @var string|null $name
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string|null $description
     *
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $description;

    /**
     * @var boolean $mailing
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "1"})
     *
     */
    private $display = true;

    /**
     * @var Association[]|iterable
     * @ORM\ManyToMany(targetEntity="AcMarche\Volontariat\Entity\Association", mappedBy="secteurs")
     * @ORM\OrderBy({"nom": "ASC"})
     */
    protected $associations;

    /**
     * @var Volontaire[]|iterable
     * @ORM\ManyToMany(targetEntity="AcMarche\Volontariat\Entity\Volontaire", mappedBy="secteurs")
     * @ORM\OrderBy({"name": "ASC"})
     */
    protected $volontaires;

    public function __toString()
    {
        return $this->getName();
    }

    public function toStringWithDescription()
    {
        $txt = $this->getName();
        if ($this->getDescription()) {
            $txt .= ' ('.$this->getDescription().')';
        }

        return $txt;
    }

    /**
     * STOP
     */


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->associations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->volontaires = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Secteur
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Secteur
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add association.
     *
     * @param \AcMarche\Volontariat\Entity\Association $association
     *
     * @return Secteur
     */
    public function addAssociation(\AcMarche\Volontariat\Entity\Association $association)
    {
        $this->associations[] = $association;

        return $this;
    }

    /**
     * Remove association.
     *
     * @param \AcMarche\Volontariat\Entity\Association $association
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAssociation(\AcMarche\Volontariat\Entity\Association $association)
    {
        return $this->associations->removeElement($association);
    }

    /**
     * Get associations.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * Add volontaire.
     *
     * @param \AcMarche\Volontariat\Entity\Volontaire $volontaire
     *
     * @return Secteur
     */
    public function addVolontaire(\AcMarche\Volontariat\Entity\Volontaire $volontaire)
    {
        $this->volontaires[] = $volontaire;

        return $this;
    }

    /**
     * Remove volontaire.
     *
     * @param \AcMarche\Volontariat\Entity\Volontaire $volontaire
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVolontaire(\AcMarche\Volontariat\Entity\Volontaire $volontaire)
    {
        return $this->volontaires->removeElement($volontaire);
    }

    /**
     * Get volontaires.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVolontaires()
    {
        return $this->volontaires;
    }

    public function getDisplay(): ?bool
    {
        return $this->display;
    }

    public function setDisplay(bool $display): self
    {
        $this->display = $display;

        return $this;
    }
}
