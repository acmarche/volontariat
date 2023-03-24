<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Repository\BesoinRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/besoin')]
class BesoinController extends AbstractController
{
    public function __construct(private BesoinRepository $besoinRepository)
    {
    }

    #[Route(path: '/', name: 'volontariat_besoin')]
    public function indexAction(): Response
    {
        $besoins = $this->besoinRepository->findAll();

        return $this->render(
            '@Volontariat/besoin/index.html.twig',
            [
                'besoins' => $besoins,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'volontariat_besoin_show', methods: ['GET'])]
    public function showAction(Besoin $besoin): Response
    {
        return $this->render(
            '@Volontariat/besoin/show.html.twig',
            [
                'association' => $besoin->getAssociation(),
                'besoin' => $besoin,
            ]
        );
    }
}
