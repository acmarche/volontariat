<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 8/02/17
 * Time: 12:36
 */

namespace AcMarche\Volontariat\Entity;

use Doctrine\DBAL\Types\Types;
use AcMarche\Volontariat\Repository\TemoignageRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TemoignageRepository::class)]
#[ORM\Table(name: 'temoignage')]
class Temoignage implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    protected string $nom;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    protected string $village;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    protected ?string $message;

    #[ORM\Column(type: Types::STRING, nullable: true)]
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
     */
    public function setNom(string $nom): static
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
     */
    public function setVillage(string $village): static
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
    public function setMessage(?string $message): static
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
    public function setUser(?string $user): void
    {
        $this->user = $user;
    }
}
