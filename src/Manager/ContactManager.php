<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/10/18
 * Time: 16:13
 */

namespace AcMarche\Volontariat\Manager;


use AcMarche\Volontariat\Entity\Security\User;

class ContactManager
{
    /**
     * @var string|null $nom
     */
    protected $nom;
    /**
     * @var string|null $sujet
     */
    protected $sujet;
    /**
     * @var string|null $contenu
     */
    protected $contenu;
    /**
     * @var string|null $email
     */
    protected $email;
    /**
     * @var string|null $destinataire
     */
    protected $destinataire;
    /**
     * @var string|null $association_nom
     */
    protected $association_nom;

    public function populateFromUser(User $user): void
    {
        $this->email = $user->getEmail();
        $this->nom = $user->getPrenom().' '.$user->getNom();
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(?string $sujet): void
    {
        $this->sujet = $sujet;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): void
    {
        $this->contenu = $contenu;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getDestinataire(): ?string
    {
        return $this->destinataire;
    }

    public function setDestinataire(?string $destinataire): void
    {
        $this->destinataire = $destinataire;
    }

    public function getAssociationNom(): ?string
    {
        return $this->association_nom;
    }

    public function setAssociationNom(?string $association_nom): void
    {
        $this->association_nom = $association_nom;
    }
}