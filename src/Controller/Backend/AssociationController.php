<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Form\AssociationPublicType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use AcMarche\Volontariat\Security\RolesEnum;
#[IsGranted(RolesEnum::association->value)]
class AssociationController extends AbstractController
{
    use getAssociationTrait;

    public function __construct(
        private AssociationRepository $associationRepository,
    ) {
    }

    #[Route(path: '/backend/association/edit', name: 'volontariat_backend_association_edit')]
    public function edit(Request $request): Response
    {
        if (($hasAssociation = $this->hasAssociation()) instanceof Response) {
            return $hasAssociation;
        }

        $form = $this->createForm(AssociationPublicType::class, $this->association);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationRepository->flush();

            $this->addFlash('success', 'L\' association a bien été modifiée');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);


        return $this->render(
            '@Volontariat/backend/association/edit.html.twig',
            [
                'association' => $this->association,
                'form' => $form,
            ],
            $response
        );
    }
}
