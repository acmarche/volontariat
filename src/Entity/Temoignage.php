<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 8/02/17
 * Time: 12:36
 */

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Repository\TemoignageRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TemoignageRepository::class)]
#[ORM\Table(name: 'temoignage')]
class Temoignage implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    protected string $nom;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    protected string $village;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    protected ?string $message;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $user = null;

    public function __toString(): string
    {
        return $this->getNom();
    }

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set prenom.
     *
     * @param string $nom
     */
    public function setNom($nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get prenom.
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Set village.
     *
     * @param string $village
     */
    public function setVillage($village): static
    {
        $this->village = $village;

        return $this;
    }

    /**
     * Get village.
     */
    public function getVillage(): ?string
    {
        return $this->village;
    }

    /**
     * Set message.
     *
     * @param string $message
     */
    public function setMessage($message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getUser(): ?string
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
