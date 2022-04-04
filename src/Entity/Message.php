<?php

namespace AcMarche\Volontariat\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'message')]
#[ORM\HasLifecycleCallbacks]
class Message implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $sujet;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    protected ?string $contenu;

    protected array $selection_destinataires;

    protected array $destinataires;

    protected array $froms;

    protected ?File $file;

    protected ?string $nom;

    protected ?string $nom_destinataire;

    public function __toString(): string
    {
        return $this->getSujet();
    }

    public function getSelectionDestinataires(): iterable
    {
        return $this->selection_destinataires;
    }

    public function setSelectionDestinataires(array $selection_destinataires): void
    {
        $this->selection_destinataires = $selection_destinataires;
    }

    public function getDestinataires(): iterable
    {
        return $this->destinataires;
    }

    public function setDestinataires(array $destinataires): void
    {
        $this->destinataires = $destinataires;
    }

    public function getFroms(): ?iterable
    {
        return $this->froms;
    }

    public function setFroms(array $froms): void
    {
        $this->froms = $froms;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getNomDestinataire(): ?string
    {
        return $this->nom_destinataire;
    }

    public function setNomDestinataire(?string $nom_destinataire): void
    {
        $this->nom_destinataire = $nom_destinataire;
    }

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set sujet.
     *
     * @param string $sujet
     */
    public function setSujet($sujet): static
    {
        $this->sujet = $sujet;

        return $this;
    }

    /**
     * Get sujet.
     */
    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    /**
     * Set contenu.
     *
     * @param string $contenu
     */
    public function setContenu($contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu.
     */
    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }
}
