<?php

namespace AcMarche\Volontariat\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Form\Admin\BesoinType;
use AcMarche\Volontariat\Form\BesoinPublicType;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

#[Route(path: '/besoin')]
class BesoinController extends AbstractController
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }
    #[Route(path: '/', name: 'volontariat_besoin')]
    public function indexAction() : Response
    {
        $em = $this->managerRegistry->getManager();
        $besoins = $em->getRepository(Besoin::class)->findAll();
        return $this->render(
            '@Volontariat/besoin/index.html.twig',
            [
                'besoins' => $besoins,
            ]
        );
    }
    /**
     * Displays a form to edit an existing Volontaire entity.
     *
     *
     */
    #[Route(path: '/{id}', name: 'volontariat_besoin_show', methods: ['GET'])]
    public function showAction(Besoin $besoin) : Response
    {
        return $this->render(
            '@Volontariat/besoin/show.html.twig',
            array(
                'association' => $besoin->getAssociation(),
                'besoin' => $besoin,
            )
        );
    }
}
