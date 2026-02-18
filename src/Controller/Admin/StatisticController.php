<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Security\RolesEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::admin->value)]
#[Route(path: '/admin/statistic/')]
class StatisticController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route(path: '/', name: 'volontariat_admin_statistic', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@Volontariat/admin/statistic/index.html.twig',
            [
            ]
        );
    }
}
