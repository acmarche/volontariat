<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Validator\Constraints as AcMarcheAssert;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Association
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\AssociationRepository")
 * @ORM\Table(name="association")
 * @Vich\Uploadable
 */
class Association implements Uploadable, TimestampableInterface
{
    use TimestampableTrait;

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
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null $slug
     */
    protected $slug;

    /**
     * @var string|null $slugname
     * Gedmo\Slug(fields={"nom"}, separator="-", updatable=true)
     * @ORM\Column(length=70, unique=true)
     */
    private $slugname;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null $address
     */
    protected $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null $number
     */
    protected $number;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @AcMarcheAssert\CodePostalIsBelgium
     * @var integer|null $postalCode
     */
    protected $postalCode;

    /**
     * @var string|null $city
     *
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $city;

    /**
     * @var string|null $email
     * @ORM\Column(name="email")
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null $web_site
     */
    protected $web_site;

    /**
     * @var string|null $phone
     * @ORM\Column(type="string", nullable=true )
     *
     */
    protected $phone;

    /**
     * @var string|null $mobile
     *
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $mobile;

    /**
     * @var string|null $fax
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $fax;

    /**
     * Description générale de l'association
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(max=600)
     * @var string|null $description
     */
    protected $description;

    /**
     * Description des besoins permanent
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null $requirement
     */
    protected $requirement;

    /**
     * lieu besoins permanents
     *
     * @ORM\Column(type="text", nullable=true)
     * @var string|null $place
     */
    protected $place;

    /**
     * contact besoins permanents
     *
     * @ORM\Column(type="text", nullable=true)
     * @var string|null $contact
     */
    protected $contact;

    /**
     * @var Secteur[]|iterable $secteurs
     * @ORM\ManyToMany(targetEntity="AcMarche\Volontariat\Entity\Secteur", inversedBy="associations")
     */
    protected $secteurs;

    /**
     * @var Besoin[]|iterable $besoins
     * @ORM\OneToMany(targetEntity="AcMarche\Volontariat\Entity\Besoin", mappedBy="association", cascade={"remove"})
     */
    protected $besoins;

    /**
     * @var Activite[]|iterable $activites
     * @ORM\OneToMany(targetEntity="AcMarche\Volontariat\Entity\Activite", mappedBy="association", cascade={"remove"})
     */
    protected $activites;

    /**
     * @var User|null $user
     * @ORM\ManyToOne(targetEntity="AcMarche\Volontariat\Entity\Security\User", cascade={"persist"})
     *
     */
    protected $user;

    /**
     * @var boolean $valider
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     *
     */
    private $valider = false;

    /**
     * @var boolean $mailing
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "1"})
     *
     */
    private $mailing = true;

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
        return 'association';
    }

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="association_file", fileNameProperty="fileName", size="fileSize")
     *
     * @var File|null $fileFile
     */
    private $fileFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null $fileName
     */
    private $fileName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null $fileDescriptif
     */
    private $fileDescriptif;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer|null $fileSize
     */
    private $fileSize;

    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setFileFile(?File $file = null): void
    {
        $this->fileFile = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            try {
                $this->updatedAt = new \DateTimeImmutable();
            } catch (\Exception $e) {
            }
        }
    }

    public function getFileFile(): ?File
    {
        return $this->fileFile;
    }

    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileSize(?int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * @return string
     */
    public function getFileDescriptif(): ?string
    {
        return $this->fileDescriptif;
    }

    /**
     * @param string $fileDescriptif
     */
    public function setFileDescriptif(string $fileDescriptif): void
    {
        $this->fileDescriptif = $fileDescriptif;
    }

    /**
     * @var array $images
     */
    private $images;

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param array $images
     */
    public function setImages($images): void
    {
        $this->images = $images;
    }

    /**
     * @return string|null
     */
    public function getFirstImage()
    {
        if (count($this->images) > 0) {
            return $this->images[0];
        }

        return null;
    }

    /**
     * STOP
     */


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->secteurs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->besoins = new \Doctrine\Common\Collections\ArrayCollection();
        $this->activites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = [];
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
     * Set nom.
     *
     * @param string $nom
     *
     * @return Association
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set slug.
     *
     * @param string|null $slug
     *
     * @return Association
     */
    public function setSlug($slug = null)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string|null
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slugname.
     *
     * @param string $slugname
     *
     * @return Association
     */
    public function setSlugname($slugname)
    {
        $this->slugname = $slugname;

        return $this;
    }

    /**
     * Get slugname.
     *
     * @return string
     */
    public function getSlugname()
    {
        return $this->slugname;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return Association
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
     * @param string|null $number
     *
     * @return Association
     */
    public function setNumber($number = null)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     *
     * @return string|null
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
     * @return Association
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
     * @param string|null $city
     *
     * @return Association
     */
    public function setCity($city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string|null
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
     * @return Association
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
     * Set webSite.
     *
     * @param string|null $webSite
     *
     * @return Association
     */
    public function setWebSite($webSite = null)
    {
        $this->web_site = $webSite;

        return $this;
    }

    /**
     * Get webSite.
     *
     * @return string|null
     */
    public function getWebSite()
    {
        return $this->web_site;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     *
     * @return Association
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
     * @return Association
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
     * @return Association
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
     * Set description.
     *
     * @param string|null $description
     *
     * @return Association
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
     * Set requirement.
     *
     * @param string|null $requirement
     *
     * @return Association
     */
    public function setRequirement($requirement = null)
    {
        $this->requirement = $requirement;

        return $this;
    }

    /**
     * Get requirement.
     *
     * @return string|null
     */
    public function getRequirement()
    {
        return $this->requirement;
    }

    /**
     * Set place.
     *
     * @param string|null $place
     *
     * @return Association
     */
    public function setPlace($place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place.
     *
     * @return string|null
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set contact.
     *
     * @param string|null $contact
     *
     * @return Association
     */
    public function setContact($contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact.
     *
     * @return string|null
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set valider.
     *
     * @param bool $valider
     *
     * @return Association
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
     * @return Association
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
     * Add secteur.
     *
     * @param \AcMarche\Volontariat\Entity\Secteur $secteur
     *
     * @return Association
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
     * Add besoin.
     *
     * @param \AcMarche\Volontariat\Entity\Besoin $besoin
     *
     * @return Association
     */
    public function addBesoin(\AcMarche\Volontariat\Entity\Besoin $besoin)
    {
        $this->besoins[] = $besoin;

        return $this;
    }

    /**
     * Remove besoin.
     *
     * @param \AcMarche\Volontariat\Entity\Besoin $besoin
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBesoin(\AcMarche\Volontariat\Entity\Besoin $besoin)
    {
        return $this->besoins->removeElement($besoin);
    }

    /**
     * Get besoins.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBesoins()
    {
        return $this->besoins;
    }

    /**
     * Set user.
     *
     * @param \AcMarche\Volontariat\Entity\Security\User|null $user
     *
     * @return Association
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
     * Add activite.
     *
     * @param \AcMarche\Volontariat\Entity\Activite $activite
     *
     * @return Association
     */
    public function addActivite(\AcMarche\Volontariat\Entity\Activite $activite)
    {
        $this->activites[] = $activite;

        return $this;
    }

    /**
     * Remove activite.
     *
     * @param \AcMarche\Volontariat\Entity\Activite $activite
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeActivite(\AcMarche\Volontariat\Entity\Activite $activite)
    {
        return $this->activites->removeElement($activite);
    }

    /**
     * Get activites.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivites()
    {
        return $this->activites;
    }

    public function getMailing(): ?bool
    {
        return $this->mailing;
    }

    public function setMailing(bool $mailing): self
    {
        $this->mailing = $mailing;

        return $this;
    }
}
