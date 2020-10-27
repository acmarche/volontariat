<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Validator\Constraints as AcMarcheAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Volontaire
 * @ORM\Table(name="volontaire")
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\VolontaireRepository")
 *
 */
class Volontaire implements Uploadable, TimestampableInterface, SluggableInterface
{
    use TimestampableTrait;
    use SluggableTrait;

    /**
     * @var integer|null $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string|null $civility
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $civility;

    /**
     * @var string|null $name
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull()
     * @var string|null $surname
     */
    protected $surname;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null $slug
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string |null $address
     */
    protected $address;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     *
     * @var integer|null $number
     */
    protected $number;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\NotBlank
     * @AcMarcheAssert\CodePostalIsBelgium
     *
     * @var integer|null $postalCode
     */
    protected $postalCode;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string|null $city
     */
    protected $city;

    /**
     * @var string|null
     * @ORM\Column(name="email")
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=true )
     *
     * @var string|null $phone
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null $mobile
     */
    protected $mobile;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null $fax
     */
    protected $fax;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Type("\DateTime")
     * @var \DateTime|null $birthday
     */
    protected $birthday;

    /**
     * Métier actuel ou ancien job
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null $job
     */
    protected $job;

    /**
     * Secteur (version libre)
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     *
     */
    protected $secteur;

    /**
     * Disponible quand (we, apres journee)
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null $availability
     */
    protected $availability;

    /**
     * dispose d'un véhicule
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null $car
     */
    protected $car;

    /**
     * @var Vehicule[]|iterable $vehicules
     * @ORM\ManyToMany(targetEntity="AcMarche\Volontariat\Entity\Vehicule", inversedBy="volontaires", cascade={"persist"})
     * ORM\JoinTable(name="v")
     */
    private $vehicules;

    /**
     * volontaire connu par
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null $known_by
     */
    protected $known_by;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null $description
     */
    protected $description;

    /**
     * @var Secteur[]|iterable $secteurs
     * @ORM\ManyToMany(targetEntity="AcMarche\Volontariat\Entity\Secteur", inversedBy="volontaires")
     * @ORM\OrderBy({"name"="ASC"})
     */
    protected $secteurs;

    /**
     * @var Association|null $association
     * membres des association
     * @ORM\ManyToMany(targetEntity="AcMarche\Volontariat\Entity\Association")
     *
     */
    private $association;

    /**
     * @var User|null $user
     * @ORM\ManyToOne(targetEntity="AcMarche\Volontariat\Entity\Security\User", cascade={"persist"})
     *
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "1"})
     *
     */
    private $valider = true;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $inactif;

    /**
     *
     * @Assert\Image(
     *     maxSize = "5M"
     * )
     * @var UploadedFile|null $image
     */
    protected $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null $imageName
     */
    protected $imageName;

    /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setImage(File $file = null)
    {
        $this->image = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    public function getPath()
    {
        return 'volontaire';
    }

    public function __toString()
    {
        return $this->getSurname().' '.$this->getName();
    }

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null $notes
     */
    protected $notes;

    public function getSluggableFields(): array
    {
        return ['name', 'surname'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    /**
     * STOP
     */

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->vehicules = new ArrayCollection();
        $this->secteurs = new ArrayCollection();
        $this->association = new ArrayCollection();
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
     * Set civility.
     *
     * @param string|null $civility
     *
     * @return Volontaire
     */
    public function setCivility($civility = null)
    {
        $this->civility = $civility;

        return $this;
    }

    /**
     * Get civility.
     *
     * @return string|null
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Volontaire
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
     * Set surname.
     *
     * @param string|null $surname
     *
     * @return Volontaire
     */
    public function setSurname($surname = null)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname.
     *
     * @return string|null
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return Volontaire
     */
    public function setAddress($address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set number.
     *
     * @param int|null $number
     *
     * @return Volontaire
     */
    public function setNumber($number = null)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     *
     * @return int|null
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set postalCode.
     *
     * @param int|null $postalCode
     *
     * @return Volontaire
     */
    public function setPostalCode($postalCode = null)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode.
     *
     * @return int|null
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return Volontaire
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Volontaire
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     *
     * @return Volontaire
     */
    public function setPhone($phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set mobile.
     *
     * @param string|null $mobile
     *
     * @return Volontaire
     */
    public function setMobile($mobile = null)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile.
     *
     * @return string|null
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set fax.
     *
     * @param string|null $fax
     *
     * @return Volontaire
     */
    public function setFax($fax = null)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax.
     *
     * @return string|null
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set birthday.
     *
     * @param \DateTime|null $birthday
     *
     * @return Volontaire
     */
    public function setBirthday($birthday = null)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday.
     *
     * @return \DateTime|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set job.
     *
     * @param string|null $job
     *
     * @return Volontaire
     */
    public function setJob($job = null)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job.
     *
     * @return string|null
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set secteur.
     *
     * @param string|null $secteur
     *
     * @return Volontaire
     */
    public function setSecteur($secteur = null)
    {
        $this->secteur = $secteur;

        return $this;
    }

    /**
     * Get secteur.
     *
     * @return string|null
     */
    public function getSecteur()
    {
        return $this->secteur;
    }

    /**
     * Set availability.
     *
     * @param string|null $availability
     *
     * @return Volontaire
     */
    public function setAvailability($availability = null)
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * Get availability.
     *
     * @return string|null
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * Set car.
     *
     * @param string|null $car
     *
     * @return Volontaire
     */
    public function setCar($car = null)
    {
        $this->car = $car;

        return $this;
    }

    /**
     * Get car.
     *
     * @return string|null
     */
    public function getCar()
    {
        return $this->car;
    }

    /**
     * Set knownBy.
     *
     * @param string|null $knownBy
     *
     * @return Volontaire
     */
    public function setKnownBy($knownBy = null)
    {
        $this->known_by = $knownBy;

        return $this;
    }

    /**
     * Get knownBy.
     *
     * @return string|null
     */
    public function getKnownBy()
    {
        return $this->known_by;
    }

    /**
     * Set suggestion.
     *
     * @param string|null $description
     *
     * @return Volontaire
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get suggestion.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set valider.
     *
     * @param bool $valider
     *
     * @return Volontaire
     */
    public function setValider($valider)
    {
        $this->valider = $valider;

        return $this;
    }

    /**
     * Get valider.
     *
     * @return bool
     */
    public function getValider()
    {
        return $this->valider;
    }

    /**
     * Set imageName.
     *
     * @param string|null $imageName
     *
     * @return Volontaire
     */
    public function setImageName($imageName = null)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName.
     *
     * @return string|null
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * Add vehicule.
     *
     * @param \AcMarche\Volontariat\Entity\Vehicule $vehicule
     *
     * @return Volontaire
     */
    public function addVehicule(\AcMarche\Volontariat\Entity\Vehicule $vehicule)
    {
        $this->vehicules[] = $vehicule;

        return $this;
    }

    /**
     * Remove vehicule.
     *
     * @param \AcMarche\Volontariat\Entity\Vehicule $vehicule
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVehicule(\AcMarche\Volontariat\Entity\Vehicule $vehicule)
    {
        return $this->vehicules->removeElement($vehicule);
    }

    /**
     * Get vehicules.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVehicules()
    {
        return $this->vehicules;
    }

    /**
     * Add secteur.
     *
     * @param \AcMarche\Volontariat\Entity\Secteur $secteur
     *
     * @return Volontaire
     */
    public function addSecteur(\AcMarche\Volontariat\Entity\Secteur $secteur)
    {
        $this->secteurs[] = $secteur;

        return $this;
    }

    /**
     * Remove secteur.
     *
     * @param \AcMarche\Volontariat\Entity\Secteur $secteur
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeSecteur(\AcMarche\Volontariat\Entity\Secteur $secteur)
    {
        return $this->secteurs->removeElement($secteur);
    }

    /**
     * Get secteurs.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSecteurs()
    {
        return $this->secteurs;
    }

    /**
     * Add association.
     *
     * @param \AcMarche\Volontariat\Entity\Association $association
     *
     * @return Volontaire
     */
    public function addAssociation(\AcMarche\Volontariat\Entity\Association $association)
    {
        $this->association[] = $association;

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
        return $this->association->removeElement($association);
    }

    /**
     * Get association.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * Set user.
     *
     * @param \AcMarche\Volontariat\Entity\Security\User|null $user
     *
     * @return Volontaire
     */
    public function setUser(\AcMarche\Volontariat\Entity\Security\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AcMarche\Volontariat\Entity\Security\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return bool|null
     */
    public function getInactif(): ?bool
    {
        return $this->inactif;
    }

    /**
     * @param bool|null $inactif
     */
    public function setInactif(?bool $inactif): void
    {
        $this->inactif = $inactif;
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
