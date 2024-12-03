<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Validator\Constraints as AcMarcheAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Table(name: 'volontaire')]
#[ORM\Entity(repositoryClass: VolontaireRepository::class)]
#[Vich\Uploadable]
class Volontaire implements Uploadable, TimestampableInterface, SluggableInterface, \Stringable
{
    use TimestampableTrait;
    use SluggableTrait;
    use ImageTrait;
    use NotificationTrait;
    use UuidTrait;

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

    #[ORM\Column(type: 'date', nullable: true)]
    public ?\DateTimeInterface $birthday;

    #[ORM\Column(type: 'smallint', nullable: true)]
    public ?int $birthyear;
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
    #[ORM\Column(type: 'boolean', nullable: false)]
    public bool $inactif = false;
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $notes;

    #[Vich\UploadableField(mapping: 'volontaire_image', fileNameProperty: 'imageName')]
    #[Assert\Image(maxSize: '7M')]
    private ?File $image = null;

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
