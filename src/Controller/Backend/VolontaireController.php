<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\VolontairePublicType;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use AcMarche\Volontariat\Security\RolesEnum;
#[IsGranted(RolesEnum::volontaire->value)]
class VolontaireController extends AbstractController
{
    public function __construct(
        private VolontaireRepository $volontaireRepository,
    ) {
    }

    #[Route(path: '/backend/volontaire/edit', name: 'volontariat_backend_volontaire_edit')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Volontaire) {
            $this->addFlash('success', 'Aucune fiche volontaire trouvée');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        $volontaire = $user;

        $this->denyAccessUnlessGranted('edit', $volontaire);

        $form = $this->createForm(VolontairePublicType::class, $volontaire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->volontaireRepository->flush();

            $this->addFlash('success', 'Le volontaire a bien été modifié');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@Volontariat/backend/volontaire/edit.html.twig',
            [
                'volontaire' => $volontaire,
                'form' => $form,
            ],
            $response
        );
    }
}
