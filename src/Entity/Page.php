<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Repository\PageRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Marche\VolontaireBundle\Entity\Page
 *
 */
#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'page')]
class Page implements Uploadable, SluggableInterface, Stringable
{
    use SluggableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    protected string $title;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    protected ?string $content;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Type(type: 'integer')]
    protected ?int $ordre;
    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private bool $actualite = false;

    protected array $images;

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getPath(): string
    {
        return 'page';
    }

    public function getFirstImage()
    {
        $images = $this->getImages();

        return $images[0]['url'] ?? null;
    }

    public function __construct()
    {
        $this->images = [];
    }

    public function getSluggableFields(): array
    {
        return ['title'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }
    /**
     * STOP
     */
    /**
     * Get id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     */
    public function setContent($content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     */
    public function setOrdre($ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     */
    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function isActualite(): bool
    {
        return $this->actualite;
    }

    public function setActualite(bool $actualite): void
    {
        $this->actualite = $actualite;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function getActualite(): bool
    {
        return $this->actualite;
    }
}
