<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 8/02/17
 * Time: 12:36
 */

namespace AcMarche\Volontariat\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Iterable_;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Marche\VolontaireBundle\Entity\Page
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\VehiculeRepository")
 * @ORM\Table(name="vehicule")
 *
 */
class Vehicule
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
     * @var string|null $nom
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $nom;

    /**
     * @var Volontaire[]|iterable
     * @ORM\ManyToMany(targetEntity="AcMarche\Volontariat\Entity\Volontaire", mappedBy="vehicules")
     * */
    private $volontaires;

    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->volontaires = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Vehicule
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Add volontaire
     *
     * @param \AcMarche\Volontariat\Entity\Volontaire $volontaire
     *
     * @return Vehicule
     */
    public function addVolontaire(\AcMarche\Volontariat\Entity\Volontaire $volontaire)
    {
        $this->volontaires[] = $volontaire;

        return $this;
    }

    /**
     * Remove volontaire
     *
     * @param \AcMarche\Volontariat\Entity\Volontaire $volontaire
     */
    public function removeVolontaire(\AcMarche\Volontariat\Entity\Volontaire $volontaire)
    {
        $this->volontaires->removeElement($volontaire);
    }

    /**
     * Get volontaires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVolontaires()
    {
        return $this->volontaires;
    }
}
