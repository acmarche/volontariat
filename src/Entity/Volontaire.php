<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Validator\Constraints as AcMarcheAssert;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'volontaire')]
#[ORM\Entity(repositoryClass: VolontaireRepository::class)]
class Volontaire implements Uploadable, TimestampableInterface, SluggableInterface, \Stringable
{
    use TimestampableTrait;
    use SluggableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    public int $id;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $civility;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank]
    public string $name;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotNull]
    public ?string $surname;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $address;

    #[ORM\Column(type: 'smallint', nullable: true)]
    public ?int $number;
    /**
     * @AcMarcheAssert\CodePostalIsBelgium
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotBlank]
    public ?int $postalCode;

    #[ORM\Column(type: 'string', nullable: false)]
    public ?string $city;

    #[ORM\Column(name: 'email')]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    public string $email;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $phone;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $mobile;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $fax;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Type(\DateTime::class)]
    public \DateTimeInterface $birthday;
    /**
     * Métier actuel ou ancien job.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $job;
    /**
     * Secteur (version libre).
     */
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $secteur;
    /**
     * Disponible quand (we, apres journee).
     */
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $availability;
    /**
     * dispose d'un véhicule.
     */
    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $car;
    /**
     * @var Vehicule[]|iterable $vehicules
     */
    #[ORM\ManyToMany(targetEntity: Vehicule::class, inversedBy: 'volontaires', cascade: ['persist'])]
    public Collection $vehicules;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $known_by;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description;
    /**
     * @var Secteur[]|iterable $secteurs
     */
    #[ORM\ManyToMany(targetEntity: Secteur::class, inversedBy: 'volontaires')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    public Collection $secteurs;
    /**
     * membres des association.
     *
     * @var Association[] $association
     */
    #[ORM\ManyToMany(targetEntity: Association::class)]
    public Collection|null $association = null;
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    public ?User $user = null;
    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 1])]
    public bool $valider = true;
    #[ORM\Column(type: 'boolean', nullable: true)]
    public ?bool $inactif = null;
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $notes;

    #[Assert\Image(maxSize: '5M')]
    public ?File $image;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $imageName;

    public function setImage(File|UploadedFile $file = null): void
    {
        $this->image = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
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
        return $this->surname.' '.$this->name;
    }

    public function getSluggableFields(): array
    {
        return ['name', 'surname'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function __construct()
    {
        $this->vehicules = new ArrayCollection();
        $this->secteurs = new ArrayCollection();
        $this->association = new ArrayCollection();
    }

    public static function newFromUser(User $user): self
    {
        $voluntary = new self();
        $voluntary->name = $user->name;
        $voluntary->surname = $user->surname;
        $voluntary->email = $user->email;
        $voluntary->city = $user->city;
        $voluntary->user = $user;

        return $voluntary;
    }

    public function getId()
    {
        return $this->id;
    }
}
