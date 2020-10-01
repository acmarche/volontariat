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

    public function populateFromUser(User $user)
    {
        $this->email = $user->getEmail();
        $this->nom = $user->getPrenom().' '.$user->getNom();
    }

    /**
     * @return null|string
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param null|string $nom
     */
    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return null|string
     */
    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    /**
     * @param null|string $sujet
     */
    public function setSujet(?string $sujet): void
    {
        $this->sujet = $sujet;
    }

    /**
     * @return null|string
     */
    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    /**
     * @param null|string $contenu
     */
    public function setContenu(?string $contenu): void
    {
        $this->contenu = $contenu;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return null|string
     */
    public function getDestinataire(): ?string
    {
        return $this->destinataire;
    }

    /**
     * @param null|string $destinataire
     */
    public function setDestinataire(?string $destinataire): void
    {
        $this->destinataire = $destinataire;
    }

    /**
     * @return null|string
     */
    public function getAssociationNom(): ?string
    {
        return $this->association_nom;
    }

    /**
     * @param null|string $association_nom
     */
    public function setAssociationNom(?string $association_nom): void
    {
        $this->association_nom = $association_nom;
    }
}