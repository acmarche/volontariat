<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'volontariat_admin_home')]
    public function indexAction() : Response
    {
        return $this->render('@Volontariat/admin/default/index.html.twig');
    }
}
