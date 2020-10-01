<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Service\AssociationService;
use AcMarche\Volontariat\Service\VolontaireService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 * @package AppBundle\Controller
 * @Route("/dashboard")
 * @IsGranted("ROLE_VOLONTARIAT")
 */
class DashboardController extends AbstractController
{
    /**
     * @var AssociationService
     */
    private $associationService;
    /**
     * @var VolontaireService
     */
    private $volontaireService;

    public function __construct(AssociationService $associationService, VolontaireService $volontaireService)
    {
        $this->associationService = $associationService;
        $this->volontaireService = $volontaireService;
    }

    /**
     * @Route("/",name="volontariat_dashboard")
     *
     */
    public function indexAction()
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
