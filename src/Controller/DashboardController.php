<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_VOLONTARIAT')]
class DashboardController extends AbstractController
{
    public function __construct(
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository
    ) {
    }

    #[Route(path: '/dashboard/', name: 'volontariat_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();

        $association = $this->associationRepository->findAssociationByUser($user);
        $volontaire = $this->volontaireRepository->findVolontaireByUser($user);

        return $this->render('@Volontariat/dashboard/index.html.twig', [
            'volontaire' => $volontaire,
            'association' => $association,
            'user' => $user,
        ]);
    }
}
