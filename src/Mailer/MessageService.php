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

    public function getDestinataires(string $query)
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

}
