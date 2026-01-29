<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Security\RolesEnum;
use DateTimeInterface;
use Stringable;
use Doctrine\DBAL\Types\Types;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: AssociationRepository::class)]
#[ORM\Table(name: 'association')]
class Association implements Uploadable, TimestampableInterface, SluggableInterface, Stringable, UserInterface, PasswordHasherAwareInterface, LegacyPasswordAuthenticatedUserInterface
{
    use TimestampableTrait;
    use SluggableTrait;
    use ImageTrait;
    use UuidTrait;
    use PasswordAuthenticableTrait;

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    public int $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    public ?string $name;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    protected $slug;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $address;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $number;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $postalCode;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $city;

    #[ORM\Column(name: 'email')]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    public ?string $email;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $web_site;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $phone;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $mobile;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 900)]
    public ?string $description;

    /**
     * Description des besoins permanent.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $requirement;

    /**
     * lieu besoins permanents.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $place;

    /**
     * contact besoins permanents.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $contact;

    /**
     * @var Secteur[]|iterable $secteurs
     */
    #[ORM\ManyToMany(targetEntity: Secteur::class, inversedBy: 'associations')]
    public Collection $secteurs;

    /**
     * @var Besoin[]|iterable $besoins
     */
    #[ORM\OneToMany(mappedBy: 'association', targetEntity: Besoin::class, cascade: ['remove'])]
    public Collection $besoins;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    public ?bool $accord = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?DateTimeInterface $accord_date = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => 0])]
    public bool $valider = false;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => 1])]
    public bool $notification_new_voluntary = true;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => 1])]
    public bool $notification_message_association = true;

    #[Vich\UploadableField(mapping: 'association_image', fileNameProperty: 'imageName')]
    #[Assert\Image(maxSize: '7M')]
    private ?File $image = null;


    #[ORM\ManyToOne(targetEntity: User::class)]
    public ?User $user = null;

    public function getPath(): string
    {
        return 'association';
    }

    public function __construct()
    {
        $this->secteurs = new ArrayCollection();
        $this->besoins = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public array $images = [];

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function getFirstImage(): ?array
    {
        if ([] !== $this->images) {
            return $this->images[0];
        }

        return null;
    }

    public function getSluggableFields(): array
    {
        return ['name'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function getRoles(): array
    {
        if (!$this->valider) {
            return [];
        }

        return [RolesEnum::association->value];
    }

    public function getId(): int
    {
        return $this->id;
    }
}
