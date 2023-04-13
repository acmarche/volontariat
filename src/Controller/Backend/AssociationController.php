<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Form\AssociationPublicType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/backend/association')]
#[IsGranted('ROLE_VOLONTARIAT')]
class AssociationController extends AbstractController
{
    public function __construct(
        private AssociationRepository $associationRepository,
        private FileHelper $fileHelper,
    ) {
    }

    #[Route(path: '/edit', name: 'volontariat_backend_association_edit')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();

        if (!$association = $user->association) {
            $this->addFlash('success', 'Aucune fiche association trouvée');

            return $this->redirectToRoute('volontariat_dashboard');
        }
        $form = $this->createForm(AssociationPublicType::class, $association);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->fileHelper->traitementFiles($association);
            $this->associationRepository->flush();

            $this->addFlash('success', 'L\' association a bien été modifiée');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        return $this->render(
            '@Volontariat/backend/association/edit.html.twig',
            [
                'association' => $association,
                'form' => $form->createView(),
            ]
        );
    }
}
