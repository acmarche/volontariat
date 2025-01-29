<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Repository\AssociationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

trait getAssociationTrait
{
    public ?Association $association = null;

    #[Required]
    public function setTuteurUtils(AssociationRepository $associationRepository): void
    {
        $this->associationRepository = $associationRepository;
    }

    public function hasAssociation(): ?Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('volontariat_home');
        }

        try {
            if (!$this->association = $this->associationRepository->findAssociationByUser($user)) {
                $this->addFlash('danger', 'Aucune fiche association reliée à votre compte');

                return $this->redirectToRoute('volontariat_dashboard');
            }
        } catch (NonUniqueResultException $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('volontariat_dashboard');
        }

        return null;
    }
}
