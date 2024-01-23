<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Search\SearchVolontaireType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route(path: '/volontaire')]
class VolontaireController extends AbstractController
{
    public function __construct(
        private VolontaireRepository $volontaireRepository,
        private AssociationRepository $associationRepository,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    #[Route(path: '/', name: 'volontariat_volontaire')]
    public function index(Request $request): Response
    {
        $data = [];
        $search_form = $this->createForm(SearchVolontaireType::class, $data);
        $search_form->handleRequest($request);
        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
        }
        $volontaires = $this->volontaireRepository->search($data);
        if (!$this->authorizationChecker->isGranted('index')) {
            return $this->render(
                '@Volontariat/volontaire/index_not_connected.html.twig',
                [
                    'volontaires' => $volontaires,
                ]
            );
        }

        return $this->render(
            '@Volontariat/volontaire/index.html.twig',
            [
                'search_form' => $search_form->createView(),
                'volontaires' => $volontaires,
                'search' => $search_form->isSubmitted(),
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'volontariat_volontaire_show')]
    public function show(Volontaire $volontaire): Response
    {
        if (!$this->authorizationChecker->isGranted('show', $volontaire)) {
            return $this->render(
                '@Volontariat/volontaire/show_not_connected.html.twig',
                [
                    'volontaire' => $volontaire,
                ]
            );
        }

        $associations = $this->associationRepository->getAssociationsWithSameSecteur($volontaire);

        return $this->render(
            '@Volontariat/volontaire/show.html.twig',
            [
                'volontaire' => $volontaire,
                'associations' => $associations,
            ]
        );
    }
}
