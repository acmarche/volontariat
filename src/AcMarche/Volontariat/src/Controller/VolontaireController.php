<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Search\SearchVolontaireType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\Voter\VolontaireVoter;
use AcMarche\Volontariat\Seo\SeoData;
use AcMarche\Volontariat\Seo\SeoService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VolontaireController extends AbstractController
{
    public function __construct(
        private readonly VolontaireRepository $volontaireRepository,
        private readonly AssociationRepository $associationRepository,
        private readonly SeoService $seoService,
    ) {}

    #[Route(path: '/volontaire/', name: 'volontariat_volontaire')]
    public function index(Request $request): Response
    {
        $this->seoService->setData(new SeoData(
            title: 'Volontaires',
            description: 'Découvrez les volontaires inscrits sur la plate-forme du volontariat de Marche-en-Famenne.',
        ));
        $volontaires = $this->volontaireRepository->findActif();
        if (!$this->isGranted(VolontaireVoter::INDEX)) {
            return $this->render(
                '@Volontariat/volontaire/index_not_connected.html.twig',
                [
                    'volontaires' => $volontaires,
                ],
            );
        }

        return $this->render(
            '@Volontariat/volontaire/index.html.twig',
            [
                'volontaires' => $volontaires,
            ],
        );
    }

    #[Route(path: '/volontaire/{uuid}', name: 'volontariat_volontaire_show')]
    public function show(#[MapEntity(expr: 'repository.findOneByUuid(uuid)')] Volontaire $volontaire): Response
    {
        $this->seoService->setData(new SeoData(
            title: $volontaire->name.' '.$volontaire->surname,
            description: $volontaire->description ?? 'Profil du volontaire '.$volontaire->name.' '.$volontaire->surname.'.',
        ));
        if (!$this->isGranted(VolontaireVoter::SHOW, $volontaire)) {
            return $this->render(
                '@Volontariat/volontaire/show_not_connected.html.twig',
                [
                    'volontaire' => $volontaire,
                ],
            );
        }

        $associations = $this->associationRepository->getAssociationsWithSameSecteur($volontaire);

        return $this->render(
            '@Volontariat/volontaire/show.html.twig',
            [
                'volontaire' => $volontaire,
                'associations' => $associations,
            ],
        );
    }
}
