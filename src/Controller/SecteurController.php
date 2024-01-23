<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Repository\SecteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/secteur')]
class SecteurController extends AbstractController
{
    public function __construct(private SecteurRepository $secteurRepository)
    {
    }

    #[Route(path: '/', name: 'volontariat_secteur')]
    public function index(): Response
    {
        $secteurs = $this->secteurRepository->findAllOrdered();

        return $this->render('@Volontariat/secteur/index.html.twig', [
            'secteurs' => $secteurs,
        ]);
    }

    #[Route(path: '/{id}', name: 'volontariat_secteur_show', methods: ['GET'])]
    public function show(Secteur $secteur): Response
    {
        $associations = $secteur->getAssociations();
        $volontaires = $secteur->getVolontaires();

        return $this->render('@Volontariat/secteur/show.html.twig', [
            'secteur' => $secteur,
            'associations' => $associations,
            'volontaires' => $volontaires,
        ]);
    }
}
