<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\InterfaceDef\Uploadable;
use AcMarche\Volontariat\Repository\PageRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'page')]
class Page implements Uploadable, SluggableInterface, \Stringable
{
    use SluggableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    public int $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    public string $title;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $excerpt;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    public ?string $content;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Type(type: 'integer')]
    public ?int $ordre;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    public bool $actualite = false;

    public array $images;

    public function __toString(): string
    {
        return $this->title;
    }

    public function getPath(): string
    {
        return 'page';
    }

    public function getFirstImage()
    {
        $images = $this->images;

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

    public function getId()
    {
        return $this->id;
    }
}
