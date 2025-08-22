<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Search\SearchVolontaireType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\SecurityData;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class VolontaireController extends AbstractController
{
    public function __construct(
        private VolontaireRepository $volontaireRepository,
        private AssociationRepository $associationRepository,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    #[Route(path: '/volontaire/', name: 'volontariat_volontaire')]
    public function index(Request $request): Response
    {
        $data = [];
        $form = $this->createForm(SearchVolontaireType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
        }

        $volontaires = $this->volontaireRepository->search($data);
        if (!$this->authorizationChecker->isGranted(SecurityData::getRoleAssociation())) {
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
                'search_form' => $form,
                'volontaires' => $volontaires,
                'search' => $form->isSubmitted(),
            ],
        );
    }

    #[Route(path: '/volontaire/{uuid}', name: 'volontariat_volontaire_show')]
    public function show(#[MapEntity(expr: 'repository.findOneByUuid(uuid)')] Volontaire $volontaire): Response
    {
        if (!$this->authorizationChecker->isGranted('show', $volontaire)) {
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
