<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Association;
use Symfony\Component\HttpFoundation\Response;

trait getAssociationTrait
{
    public ?Association $association = null;

    public function hasAssociation(): ?Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('volontariat_home');
        }

        if (!$user instanceof Association) {
            $this->addFlash('danger', 'Aucune fiche association reliée à votre compte');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        if (!$user->valider) {
            $this->addFlash('danger', 'Votre association n\'est pas encore validée');

            return null;
        }

        $this->association = $user;

        return null;
    }
}
