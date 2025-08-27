<?php

namespace AcMarche\Volontariat\Mailer;

use AcMarche\Volontariat\Entity\Association;
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
        return match ($query) {
            'association' => $this->associationRepository->search(['valider' => true]),
            'volontaire' => $this->volontaireRepository->findActif(),
            default => [],
        };
    }

    public function getEmailEntity(Association|Volontaire $entity): ?string
    {
        if ($entity->email) {
            return $entity->email;
        }

        return $entity->user?->email;
    }

}
