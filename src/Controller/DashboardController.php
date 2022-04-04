<?php

namespace AcMarche\Volontariat\Controller;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Volontariat\Service\AssociationService;
use AcMarche\Volontariat\Service\VolontaireService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 * @package AppBundle\Controller
 */
#[Route(path: '/dashboard')]
#[IsGranted('ROLE_VOLONTARIAT')]
class DashboardController extends AbstractController
{
    public function __construct(private AssociationService $associationService, private VolontaireService $volontaireService)
    {
    }
    #[Route(path: '/', name: 'volontariat_dashboard')]
    public function indexAction() : Response
    {
        $user = $this->getUser();
        $associations = $this->associationService->getAssociationsByUser($user);
        $volontaires = $this->volontaireService->getVolontairesByUser($user);
        return $this->render('@Volontariat/dashboard/index.html.twig', [
            'volontaires' => $volontaires,
            'tab_active' => 'profil',
            'associations' => $associations,
        ]);
    }
}
