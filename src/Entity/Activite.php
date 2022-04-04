<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Repository\ActiviteRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActiviteRepository::class)]
#[ORM\Table(name: 'activite')]
class Activite implements Uploadable, TimestampableInterface, SluggableInterface, Stringable
{
    use TimestampableTrait;
    use SluggableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    protected string $titre;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    protected ?string $content;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    protected ?string $lieu;
    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private ?bool $valider = false;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    protected ?User $user;
    /**
     * @var Association|null $association
     */
    #[ORM\ManyToOne(targetEntity: Association::class, inversedBy: 'activites')]
    #[ORM\JoinColumn(nullable: false)]
    protected array $association;

    protected array $images;

    public function __toString(): string
    {
        return $this->getTitre();
    }

    public function getFirstImage()
    {
        $images = $this->getImages();

        return $images[0]['url'] ?? null;
    }

    public function getSluggableFields(): array
    {
        return ['titre'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
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
     * Set titre.
     *
     * @param string $titre
     */
    public function setTitre($titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre.
     */
    public function getTitre(): string
    {
        return $this->titre;
    }

    /**
     * Set content.
     *
     * @param string $content
     */
    public function setContent($content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set lieu.
     *
     * @param string $lieu
     */
    public function setLieu($lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Get lieu.
     */
    public function getLieu(): ?string
    {
        return $this->lieu;
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
     * Set association.
     *
     *
     */
    public function setAssociation(Association $association): static
    {
        $this->association = $association;

        return $this;
    }

    /**
     * Get association.
     */
    public function getAssociation(): ?Association
    {
        return $this->association;
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
    public function getValider(): ?bool
    {
        return $this->valider;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function getPath(): string
    {
        return 'activite';
    }
}
