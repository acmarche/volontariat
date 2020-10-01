<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Secteur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @package AppBundle\Controller
 * @Route("/secteur")
 *
 */
class SecteurController extends AbstractController
{
    /**
     * @Route("/",name="volontariat_secteur")
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $secteurs = $em->getRepository(Secteur::class)->findAll();

        return $this->render('secteur/index.html.twig', [
            'secteurs' => $secteurs,
        ]);
    }

    /**
     * Displays a form to edit an existing Volontaire entity.
     *
     * @Route("/{id}", name="volontariat_secteur_show", methods={"GET"})
     *
     */
    public function showAction(Secteur $secteur)
    {
        $associations = $secteur->getAssociations();
        $volontaires = $secteur->getVolontaires();

        return $this->render('secteur/index.html.twig', array(
            'secteur' => $secteur,
            'associations' => $associations,
            'volontaires' => $volontaires,
        ));
    }
}
