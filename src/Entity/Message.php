<?php

namespace AcMarche\Volontariat\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="message")
 * @ORM\HasLifecycleCallbacks
 */
class Message
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
     * @var string|null $sujet
     *
     * @ORM\Column(type="string", length=255)
     *
     */
    protected $sujet;

    /**
     * contenu
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     * @var string|null $contenu
     */
    protected $contenu;

    /**
     * @var iterable
     */
    protected $selection_destinataires;

    /**
     * @var iterable
     */
    protected $destinataires;

    /**
     * @var iterable|null
     */
    protected $froms;

    /**
     * @var UploadedFile|null $file
     */
    protected $file;

    /**
     * @var string|null
     */
    protected $nom;

    /**
     * @var string|null
     */
    protected $nom_destinataire;

    public function __toString()
    {
        return $this->getSujet();
    }

    /**
     * @return mixed
     */
    public function getSelectionDestinataires()
    {
        return $this->selection_destinataires;
    }

    /**
     * @param mixed $selection_destinataires
     */
    public function setSelectionDestinataires($selection_destinataires)
    {
        $this->selection_destinataires = $selection_destinataires;
    }

    /**
     * @return array
     */
    public function getDestinataires()
    {
        return $this->destinataires;
    }

    /**
     * @param array $destinataires
     */
    public function setDestinataires($destinataires)
    {
        $this->destinataires = $destinataires;
    }

    /**
     * @return array
     */
    public function getFroms()
    {
        return $this->froms;
    }

    /**
     * @param array $froms
     */
    public function setFroms($froms): void
    {
        $this->froms = $froms;
    }

    /**
     * @return string|null
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string|null $nom
     */
    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return string|null
     */
    public function getNomDestinataire(): ?string
    {
        return $this->nom_destinataire;
    }

    /**
     * @param string|null $nom_destinataire
     */
    public function setNomDestinataire(?string $nom_destinataire): void
    {
        $this->nom_destinataire = $nom_destinataire;
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
     * Set sujet.
     *
     * @param string $sujet
     *
     * @return Message
     */
    public function setSujet($sujet)
    {
        $this->sujet = $sujet;

        return $this;
    }

    /**
     * Get sujet.
     *
     * @return string
     */
    public function getSujet()
    {
        return $this->sujet;
    }

    /**
     * Set contenu.
     *
     * @param string $contenu
     *
     * @return Message
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu.
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * @return null|UploadedFile
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param null|UploadedFile $file
     */
    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }
}
