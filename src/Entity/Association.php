<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Validator\Constraints as AcMarcheAssert;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: AssociationRepository::class)]
#[ORM\Table(name: 'association')]
class Association implements Uploadable, TimestampableInterface, SluggableInterface, Stringable
{
    public DateTimeInterface $updated;
    use TimestampableTrait;
    use SluggableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    protected ?string $nom;

    #[ORM\Column(type: 'string', nullable: true)]
    protected $slug;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $address;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $number;
    /**
     * @AcMarcheAssert\CodePostalIsBelgium
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $postalCode;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $city;

    #[ORM\Column(name: 'email')]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    protected ?string $email;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $web_site;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $phone;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $mobile;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 600)]
    protected ?string $description;
    /**
     * Description des besoins permanent
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $requirement;
    /**
     * lieu besoins permanents
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $place;
    /**
     * contact besoins permanents
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $contact;
    /**
     * @var Secteur[]|iterable $secteurs
     */
    #[ORM\ManyToMany(targetEntity: Secteur::class, inversedBy: 'associations')]
    protected Collection $secteurs;
    /**
     * @var Besoin[]|iterable $besoins
     */
    #[ORM\OneToMany(targetEntity: Besoin::class, mappedBy: 'association', cascade: ['remove'])]
    protected Collection $besoins;
    /**
     * @var Activite[]|iterable $activites
     */
    #[ORM\OneToMany(targetEntity: Activite::class, mappedBy: 'association', cascade: ['remove'])]
    protected Collection $activites;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    protected ?User $user = null;
    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private bool $valider = false;
    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 1])]
    private bool $mailing = true;

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
        return 'association';
    }

    #[Vich\UploadableField(mapping: 'association_file', fileNameProperty: 'fileName', size: 'fileSize', mimeType: 'mimeType')]
    #[Assert\File(maxSize: '20M')]
    public ?File $file = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $fileName = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $fileDescriptif = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $fileSize = null;

    public function __toString(): string
    {
        return $this->getNom();
    }

    public function setFileFile(File|UploadedFile $file = null): void
    {
        $this->fileFile = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            try {
                $this->updatedAt = new DateTimeImmutable();
            } catch (Exception) {
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

    public function setFileDescriptif(string $fileDescriptif): void
    {
        $this->fileDescriptif = $fileDescriptif;
    }

    private array $images;

    public function getImages(): array
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
        if ($this->images !== []) {
            return $this->images[0];
        }

        return null;
    }

    public function getSluggableFields(): array
    {
        return ['nom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }



    public function __construct()
    {
        $this->secteurs = new ArrayCollection();
        $this->besoins = new ArrayCollection();
        $this->activites = new ArrayCollection();
        $this->images = [];
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
     * Set nom.
     *
     * @param string $nom
     */
    public function setNom($nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     */
    public function getNom(): ?string
    {
        return $this->nom;
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
     * @param string|null $number
     */
    public function setNumber($number = null): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     */
    public function getNumber(): ?string
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
     * @param string|null $city
     */
    public function setCity($city = null): static
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
     * Set webSite.
     *
     * @param string|null $webSite
     */
    public function setWebSite($webSite = null): static
    {
        $this->web_site = $webSite;

        return $this;
    }

    /**
     * Get webSite.
     */
    public function getWebSite(): ?string
    {
        return $this->web_site;
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
     * Set description.
     *
     * @param string|null $description
     */
    public function setDescription($description = null): static
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
     * Set requirement.
     *
     * @param string|null $requirement
     */
    public function setRequirement($requirement = null): static
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
     * Set place.
     *
     * @param string|null $place
     */
    public function setPlace($place = null): static
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
     * Set contact.
     *
     * @param string|null $contact
     */
    public function setContact($contact = null): static
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact.
     */
    public function getContact(): ?string
    {
        return $this->contact;
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
     * Add besoin.
     *
     *
     */
    public function addBesoin(Besoin $besoin): static
    {
        $this->besoins[] = $besoin;

        return $this;
    }

    /**
     * Remove besoin.
     *
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBesoin(Besoin $besoin): bool
    {
        return $this->besoins->removeElement($besoin);
    }

    /**
     * Get besoins.
     */
    public function getBesoins(): iterable
    {
        return $this->besoins;
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

    /**
     * Add activite.
     *
     *
     */
    public function addActivite(Activite $activite): static
    {
        $this->activites[] = $activite;

        return $this;
    }

    /**
     * Remove activite.
     *
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeActivite(Activite $activite): bool
    {
        return $this->activites->removeElement($activite);
    }

    /**
     * Get activites.
     */
    public function getActivites(): iterable
    {
        return $this->activites;
    }

    public function getMailing(): bool
    {
        return $this->mailing;
    }

    public function setMailing(bool $mailing): self
    {
        $this->mailing = $mailing;

        return $this;
    }
}
