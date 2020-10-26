<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Marche\VolontaireBundle\Entity\Activite
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\ActiviteRepository")
 * @ORM\Table(name="activite")
 *
 */
class Activite implements Uploadable, TimestampableInterface, SluggableInterface
{
    use TimestampableTrait;
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
    protected $titre;

    /**
     * content
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     * @var string|null $content
     */
    protected $content;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     * @var string|null $lieu
     */
    protected $lieu;

    /**
     * @var boolean|null
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     *
     */
    private $valider = false;

    /**
     * @var User|null $user
     * @ORM\ManyToOne(targetEntity="AcMarche\Volontariat\Entity\Security\User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @var Association|null $association
     * @ORM\ManyToOne(targetEntity="AcMarche\Volontariat\Entity\Association", inversedBy="activites")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $association;

    /**
     * @var array
     */
    protected $images;

    public function __toString()
    {
        return $this->getTitre();
    }

    public function getFirstImage()
    {
        $images = $this->getImages();

        return isset($images[0]['url']) ? $images[0]['url'] : null;
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
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titre.
     *
     * @param string $titre
     *
     * @return Activite
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre.
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Activite
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set lieu.
     *
     * @param string $lieu
     *
     * @return Activite
     */
    public function setLieu($lieu)
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Get lieu.
     *
     * @return string
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * Set user.
     *
     * @param \AcMarche\Volontariat\Entity\Security\User|null $user
     *
     * @return Activite
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
     * Set association.
     *
     * @param \AcMarche\Volontariat\Entity\Association $association
     *
     * @return Activite
     */
    public function setAssociation(\AcMarche\Volontariat\Entity\Association $association)
    {
        $this->association = $association;

        return $this;
    }

    /**
     * Get association.
     *
     * @return \AcMarche\Volontariat\Entity\Association
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * Set valider.
     *
     * @param bool $valider
     *
     * @return Activite
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
     * @return array
     */
    public function getImages(): ?array
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

    /**
     * @return string
     */
    public function getPath()
    {
        return 'activite';
    }
}
