<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/02/18
 * Time: 10:32.
 */

namespace AcMarche\Volontariat\Mailer;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;

class MessageService
{
    public function __construct(
        private VolontaireRepository $volontaireRepository,
        private AssociationRepository $associationRepository
    ) {
    }

    public function getDestinataires($query, $isSelect = false)
    {
        switch ($query) {
            case 'association':
                return $this->associationRepository->search(['valider' => true]);

            case 'volontaire':
                return $this->volontaireRepository->search(['valider' => true]);

            default:
                return [];
        }
    }

    public function getEmailEntity(Association|Volontaire $entity): ?string
    {
        if ($entity->email) {
            return $entity->email;
        }

        if ($entity->user) {
            return $entity->user->email;
        }

        return null;
    }


    /**
     * @param Association[] $associations
     */
    public function getFroms(User $user, $associations): array
    {
        $froms = [];
        $froms[$user->getEmail()] = $user->getEmail();
        foreach ($associations as $association) {
            $froms[$association->getEmail()] = $association->getEmail();
        }

        return $froms;
    }

    public function getNom(User $user): string
    {
        $volontaires = $this->volontaireService->getVolontairesByUser($user);
        if ((is_countable($volontaires) ? count($volontaires) : 0) > 0) {
            return $volontaires[0]->getName().' '.$volontaires[0]->getSurname();
        }
        $associations = $this->associationService->getAssociationsByUser($user);
        if ([] !== $associations) {
            return $associations[0]->getNom();
        }

        return '';
    }

    public function getUser(Volontaire|Association $entity): ?User
    {
        return $entity->user;
    }
}
