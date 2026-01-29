<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class DashboardController extends AbstractController
{
    #[Route(path: '/dashboard/', name: 'volontariat_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();

        $association = null;
        $volontaire = null;

        if ($user instanceof Association) {
            $association = $user;
        } elseif ($user instanceof Volontaire) {
            $volontaire = $user;
        }

        return $this->render('@Volontariat/dashboard/index.html.twig', [
            'volontaire' => $volontaire,
            'association' => $association,
            'user' => $user,
        ]);
    }
}
