<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use AcMarche\Volontariat\Security\RolesEnum;
#[IsGranted(RolesEnum::admin->value)]
class DefaultController extends AbstractController
{
    #[Route(path: '/admin/', name: 'volontariat_admin_home')]
    public function index(): Response
    {
        return $this->render('@Volontariat/admin/default/index.html.twig');
    }

    #[Route(path: '/documentation', name: 'volontariat_documentation')]
    public function documentation(): Response
    {
        return $this->render('@Volontariat/admin/default/documentation.html.twig', [

        ]);
    }
}
