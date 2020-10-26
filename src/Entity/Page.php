<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\InterfaceDef\Uploadable;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Marche\VolontaireBundle\Entity\Page
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\PageRepository")
 * @ORM\Table(name="page")
 *
 */
class Page implements Uploadable, SluggableInterface
{
    use SluggableTrait;

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
     * @var string|null $title
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * content
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     * @var string|null $content
     */
    protected $content;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer")
     * @var integer|null $ordre
     */
    protected $ordre;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     *
     */
    private $actualite = false;

    /**
     * @var iterable
     */
    protected $images;

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return 'page';
    }

    public function getFirstImage()
    {
        $images = $this->getImages();

        return isset($images[0]['url']) ? $images[0]['url'] : null;
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
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Page
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     *
     * @return Page
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * @return bool
     */
    public function isActualite(): bool
    {
        return $this->actualite;
    }

    /**
     * @param bool $actualite
     */
    public function setActualite(bool $actualite): void
    {
        $this->actualite = $actualite;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array $images
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function getActualite(): ?bool
    {
        return $this->actualite;
    }
}
