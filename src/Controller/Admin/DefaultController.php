<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Volontariat\Controller
 * @Route("/admin")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="volontariat_admin_home")
     *
     */
    public function indexAction()
    {
        return $this->render('@Volontariat/admin/default/index.html.twig');
    }
}
