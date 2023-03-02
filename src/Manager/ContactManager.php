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
    protected ?string $nom;

    protected ?string $sujet;

    protected ?string $contenu;

    protected ?string $email;

    protected ?string $destinataire;

    protected ?string $association_nom;

    public function populateFromUser(User $user): void
    {
        $this->email = $user->getEmail();
        $this->nom = $user->getPrenom().' '.$user->getNom();
    }

}