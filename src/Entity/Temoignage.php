<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 8/02/17
 * Time: 12:36
 */

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Security\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\TemoignageRepository")
 * @ORM\Table(name="temoignage")
 *
 */
class Temoignage
{
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
     * @var string|null $nom
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $nom;

    /**
     * @var string|null $village
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $village;

    /**
     * Description
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     *
     * @var string|null $message
     */
    protected $message;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null $user
     */
    private $user;

    public function __toString()
    {
        return $this->getNom();
    }


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
     * Set prenom.
     *
     * @param string $nom
     *
     * @return Temoignage
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get prenom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set village.
     *
     * @param string $village
     *
     * @return Temoignage
     */
    public function setVillage($village)
    {
        $this->village = $village;

        return $this;
    }

    /**
     * Get village.
     *
     * @return string
     */
    public function getVillage()
    {
        return $this->village;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return Temoignage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }
}
