<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/02/18
 * Time: 10:17
 */

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;

class VolontaireService
{
    public function __construct(private VolontaireRepository $volontaireRepository, private AssociationRepository $associationRepository)
    {
    }

    public function getVolontairesByUser(User $user, $valider = false): array
    {
        $args = [];
        $args['user'] = $user;
        if ($valider) {
            $args['valider'] = true;
        }

        return $this->volontaireRepository->findBy($args);
    }

    public function hasValidVolontaire(User $user): bool
    {
        return (is_countable($this->getVolontairesByUser($user, true)) ? count($this->getVolontairesByUser($user, true)) : 0) > 0;
    }

    public function getAssociationsWithSameSecteur(Volontaire $volontaire)
    {
        $secteurs = $volontaire->getSecteurs();

        return $this->associationRepository->findAssociationBySecteur(
            $secteurs
        );
    }
}
