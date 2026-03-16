<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Repository\BesoinRepository;
use AcMarche\Volontariat\Seo\SeoData;
use AcMarche\Volontariat\Seo\SeoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BesoinController extends AbstractController
{
    public function __construct(
        private BesoinRepository $besoinRepository,
        private SeoService $seoService,
    ) {
    }

    #[Route(path: '/besoin/', name: 'volontariat_besoin')]
    public function index(): Response
    {
        $this->seoService->setData(new SeoData(
            title: 'Besoins',
            description: 'Consultez les besoins en volontaires des associations de Marche-en-Famenne.',
        ));
        $besoins = $this->besoinRepository->findAll();

        return $this->render(
            '@Volontariat/besoin/index.html.twig',
            [
                'besoins' => $besoins,
            ]
        );
    }

    #[Route(path: '/besoin/{id}', name: 'volontariat_besoin_show', methods: ['GET'])]
    public function show(Besoin $besoin): Response
    {
        $this->seoService->setData(new SeoData(
            title: (string) $besoin,
            description: $besoin->getRequirement() ?? 'Besoin en volontaire de l\'association '.$besoin->getAssociation().'.',
        ));

        return $this->render(
            '@Volontariat/besoin/show.html.twig',
            [
                'association' => $besoin->getAssociation(),
                'besoin' => $besoin,
            ]
        );
    }
}
