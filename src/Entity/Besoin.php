<?php

namespace AcMarche\Volontariat\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Besoin
 *
 * @ORM\Table(name="besoin")
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\BesoinRepository")
 *
 */
class Besoin
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
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     * @ORM\OrderBy({"intitule"="ASC"})
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTime")
     * @var \DateTime|null $date_begin
     */
    protected $date_begin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Type("\DateTime")
     * @var \DateTime|null $date_end
     */
    protected $date_end;

    /**
     * Description
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     *
     * @var string|null $requirement
     */
    protected $requirement;

    /**
     * quand
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     *
     * @var string|null $period
     */
    protected $period;

    /**
     * lieu
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     *
     * @var string|null $place
     */
    protected $place;

    /**
     * @var Association|null $association
     * @ORM\ManyToOne(targetEntity="AcMarche\Volontariat\Entity\Association", inversedBy="besoins")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $association;

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * STOP
     */


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
     * @return Besoin
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
     * Set dateBegin.
     *
     * @param \DateTime $dateBegin
     *
     * @return Besoin
     */
    public function setDateBegin($dateBegin)
    {
        $this->date_begin = $dateBegin;

        return $this;
    }

    /**
     * Get dateBegin.
     *
     * @return \DateTime
     */
    public function getDateBegin()
    {
        return $this->date_begin;
    }

    /**
     * Set dateEnd.
     *
     * @param \DateTime|null $dateEnd
     *
     * @return Besoin
     */
    public function setDateEnd($dateEnd = null)
    {
        $this->date_end = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd.
     *
     * @return \DateTime|null
     */
    public function getDateEnd()
    {
        return $this->date_end;
    }

    /**
     * Set requirement.
     *
     * @param string $requirement
     *
     * @return Besoin
     */
    public function setRequirement($requirement)
    {
        $this->requirement = $requirement;

        return $this;
    }

    /**
     * Get requirement.
     *
     * @return string
     */
    public function getRequirement()
    {
        return $this->requirement;
    }

    /**
     * Set period.
     *
     * @param string $period
     *
     * @return Besoin
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period.
     *
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set place.
     *
     * @param string $place
     *
     * @return Besoin
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place.
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set association.
     *
     * @param \AcMarche\Volontariat\Entity\Association $association
     *
     * @return Besoin
     */
    public function setAssociation(\AcMarche\Volontariat\Entity\Association $association)
    {
        $this->association = $association;

        return $this;
    }

    /**
     * Get association.
     *
     * @return \AcMarche\Volontariat\Entity\Association
     */
    public function getAssociation()
    {
        return $this->association;
    }
}
