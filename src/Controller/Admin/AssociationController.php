<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Form\Admin\AssocationType;
use AcMarche\Volontariat\Form\Search\SearchAssociationType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/association')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class AssociationController extends AbstractController
{
    public function __construct(
        private AssociationRepository $associationRepository,
        private FileHelper $fileHelper,
    ) {}

    #[Route(path: '/', name: 'volontariat_admin_association', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $data = [];
        $data['valider'] = 2;

        $search_form = $this->createForm(
            SearchAssociationType::class,
            $data,
        );
        $search_form->handleRequest($request);
        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
        }

        $associations = $this->associationRepository->search($data);

        $response = new Response(null, $search_form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@Volontariat/admin/association/index.html.twig',
            [
                'form' => $search_form->createView(),
                'associations' => $associations,
            ]
            , $response,
        );
    }

    #[Route(path: '/new', name: 'volontariat_admin_association_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $association = new Association();
        $association->setUuid($association->generateUuid());

        $form = $this->createForm(AssocationType::class, $association);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationRepository->persist($association);
            $this->associationRepository->flush();

            $this->addFlash('success', 'L\' association a bien été ajoutée');

            return $this->redirectToRoute('volontariat_admin_association_show', ['id' => $association->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/association/new.html.twig',
            [
                'association' => $association,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}/show', name: 'volontariat_admin_association_show')]
    public function show(Association $association): Response
    {
        $images = $this->fileHelper->getImages($association);

        return $this->render(
            '@Volontariat/admin/association/show.html.twig',
            [
                'association' => $association,
                'images' => $images,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_admin_association_edit')]
    public function edit(Request $request, Association $association): Response
    {
        $form = $this->createForm(AssocationType::class, $association);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationRepository->flush();

            $this->addFlash('success', 'L\' association a bien été modifiée');

            return $this->redirectToRoute('volontariat_admin_association_show', ['id' => $association->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/association/edit.html.twig',
            [
                'association' => $association,
                'form' => $form,
            ],
        );
    }


    #[Route(path: '/{id}/delete', name: 'volontariat_admin_association_delete', methods: ['POST'])]
    public function delete(Request $request, Association $association): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$association->getId(), $request->request->get('_token'))) {
            $this->associationRepository->remove($association);
            $this->associationRepository->flush();
            $this->addFlash('success', 'L\'association a bien été supprimée');
        }

        return $this->redirectToRoute('volontariat_admin_association');
    }
}
