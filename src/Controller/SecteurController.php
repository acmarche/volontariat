<?php

namespace AcMarche\Volontariat\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\Volontariat\Entity\Secteur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @package AppBundle\Controller
 *
 */
#[Route(path: '/secteur')]
class SecteurController extends AbstractController
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }
    #[Route(path: '/', name: 'volontariat_secteur')]
    public function indexAction() : Response
    {
        $em = $this->managerRegistry->getManager();
        $secteurs = $em->getRepository(Secteur::class)->findAll();
        return $this->render('@Volontariat/secteur/index.html.twig', [
            'secteurs' => $secteurs,
        ]);
    }
    /**
     * Displays a form to edit an existing Volontaire entity.
     *
     *
     */
    #[Route(path: '/{id}', name: 'volontariat_secteur_show', methods: ['GET'])]
    public function showAction(Secteur $secteur) : Response
    {
        $associations = $secteur->getAssociations();
        $volontaires = $secteur->getVolontaires();
        return $this->render('@Volontariat/secteur/index.html.twig', array(
            'secteur' => $secteur,
            'associations' => $associations,
            'volontaires' => $volontaires,
        ));
    }
}
