<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Validator\Constraints as AcMarcheAssert;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Volontaire
 *
 */
#[ORM\Table(name: 'volontaire')]
#[ORM\Entity(repositoryClass: VolontaireRepository::class)]
class Volontaire implements Uploadable, TimestampableInterface, SluggableInterface, Stringable
{
    public DateTimeInterface $updated;
    use TimestampableTrait;
    use SluggableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $civility;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    protected string $name;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotNull]
    protected ?string $surname;
    /**
     * @var string|null $slug
     */
    #[ORM\Column(type: 'string', nullable: true)]
    protected $slug;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $address;

    #[ORM\Column(type: 'smallint', nullable: true)]
    protected ?int $number;
    /**
     * @AcMarcheAssert\CodePostalIsBelgium
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotBlank]
    protected ?int $postalCode;

    #[ORM\Column(type: 'string', nullable: false)]
    protected ?string $city;

    #[ORM\Column(name: 'email')]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    protected string $email;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $phone;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $mobile;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $fax;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Type(DateTime::class)]
    protected DateTimeInterface $birthday;
    /**
     * Métier actuel ou ancien job
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $job;
    /**
     * Secteur (version libre)
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $secteur;
    /**
     * Disponible quand (we, apres journee)
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $availability;
    /**
     * dispose d'un véhicule
     */
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $car;
    /**
     * @var Vehicule[]|iterable $vehicules
     */
    #[ORM\ManyToMany(targetEntity: Vehicule::class, inversedBy: 'volontaires', cascade: ['persist'])]
    private Collection $vehicules;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $known_by;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description;
    /**
     * @var Secteur[]|iterable $secteurs
     */
    #[ORM\ManyToMany(targetEntity: Secteur::class, inversedBy: 'volontaires')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    protected Collection $secteurs;
    /**
     * membres des association
     * @var Association[] $association
     */
    #[ORM\ManyToMany(targetEntity: Association::class)]
    private Collection|null $association = null;
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    private ?User $user = null;
    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 1])]
    private bool $valider = true;
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $inactif = null;

    #[Assert\Image(maxSize: '5M')]
    protected ?File $image;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $imageName;

    public function setImage(File|UploadedFile $file = null): void
    {
        $this->image = $file;

        if ($file !== null) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new DateTime('now');
        }
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function getPath(): string
    {
        return 'volontaire';
    }

    public function __toString(): string
    {
        return $this->getSurname().' '.$this->getName();
    }

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $notes;

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
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set civility.
     *
     * @param string|null $civility
     */
    public function setCivility($civility = null): static
    {
        $this->civility = $civility;

        return $this;
    }

    /**
     * Get civility.
     */
    public function getCivility(): ?string
    {
        return $this->civility;
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
     * Set surname.
     *
     * @param string|null $surname
     */
    public function setSurname($surname = null): static
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname.
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     */
    public function setAddress($address = null): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Set number.
     *
     * @param int|null $number
     */
    public function setNumber($number = null): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * Set postalCode.
     *
     * @param int|null $postalCode
     */
    public function setPostalCode($postalCode = null): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode.
     */
    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    /**
     * Set city.
     *
     * @param string $city
     */
    public function setCity($city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Set email.
     *
     * @param string $email
     */
    public function setEmail($email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     */
    public function setPhone($phone = null): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Set mobile.
     *
     * @param string|null $mobile
     */
    public function setMobile($mobile = null): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile.
     */
    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    /**
     * Set fax.
     *
     * @param string|null $fax
     */
    public function setFax($fax = null): static
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax.
     */
    public function getFax(): ?string
    {
        return $this->fax;
    }

    /**
     * Set birthday.
     *
     *
     */
    public function setBirthday(DateTime|DateTimeImmutable $birthday = null): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday.
     *
     * @return DateTime|DateTimeImmutable|null
     */
    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * Set job.
     *
     * @param string|null $job
     */
    public function setJob($job = null): static
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job.
     */
    public function getJob(): ?string
    {
        return $this->job;
    }

    /**
     * Set secteur.
     *
     * @param string|null $secteur
     */
    public function setSecteur($secteur = null): static
    {
        $this->secteur = $secteur;

        return $this;
    }

    /**
     * Get secteur.
     */
    public function getSecteur(): ?string
    {
        return $this->secteur;
    }

    /**
     * Set availability.
     *
     * @param string|null $availability
     */
    public function setAvailability($availability = null): static
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * Get availability.
     */
    public function getAvailability(): ?string
    {
        return $this->availability;
    }

    /**
     * Set car.
     *
     * @param string|null $car
     */
    public function setCar($car = null): static
    {
        $this->car = $car;

        return $this;
    }

    /**
     * Get car.
     */
    public function getCar(): ?string
    {
        return $this->car;
    }

    /**
     * Set knownBy.
     *
     * @param string|null $knownBy
     */
    public function setKnownBy($knownBy = null): static
    {
        $this->known_by = $knownBy;

        return $this;
    }

    /**
     * Get knownBy.
     */
    public function getKnownBy(): ?string
    {
        return $this->known_by;
    }

    /**
     * Set suggestion.
     *
     * @param string|null $description
     */
    public function setDescription($description = null): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get suggestion.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set valider.
     *
     * @param bool $valider
     */
    public function setValider($valider): static
    {
        $this->valider = $valider;

        return $this;
    }

    /**
     * Get valider.
     */
    public function getValider(): bool
    {
        return $this->valider;
    }

    /**
     * Set imageName.
     *
     * @param string|null $imageName
     */
    public function setImageName($imageName = null): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName.
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * Add vehicule.
     *
     *
     */
    public function addVehicule(Vehicule $vehicule): static
    {
        $this->vehicules[] = $vehicule;

        return $this;
    }

    /**
     * Remove vehicule.
     *
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVehicule(Vehicule $vehicule): bool
    {
        return $this->vehicules->removeElement($vehicule);
    }

    /**
     * Get vehicules.
     */
    public function getVehicules(): iterable
    {
        return $this->vehicules;
    }

    /**
     * Add secteur.
     *
     *
     */
    public function addSecteur(Secteur $secteur): static
    {
        $this->secteurs[] = $secteur;

        return $this;
    }

    /**
     * Remove secteur.
     *
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeSecteur(Secteur $secteur): bool
    {
        return $this->secteurs->removeElement($secteur);
    }

    /**
     * Get secteurs.
     */
    public function getSecteurs(): iterable
    {
        return $this->secteurs;
    }

    /**
     * Add association.
     *
     *
     */
    public function addAssociation(Association $association): static
    {
        $this->association[] = $association;

        return $this;
    }

    /**
     * Remove association.
     *
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAssociation(Association $association): bool
    {
        return $this->association->removeElement($association);
    }

    /**
     * Get association.
     */
    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    /**
     * Set user.
     *
     * @param User|null $user
     */
    public function setUser(User $user = null): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getInactif(): ?bool
    {
        return $this->inactif;
    }

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
