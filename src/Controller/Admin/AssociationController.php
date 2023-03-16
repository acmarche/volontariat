<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Form\Admin\AssocationType;
use AcMarche\Volontariat\Form\FormBuilderVolontariat;
use AcMarche\Volontariat\Form\Search\SearchAssociationType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/association')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class AssociationController extends AbstractController
{
    public function __construct(
        private AssociationRepository $associationRepository,
        private FileHelper $fileHelper,
        private FormBuilderVolontariat $formBuilderVolontariat
    ) {
    }

    #[Route(path: '/', name: 'volontariat_admin_association', methods: ['GET', 'POST'])]
    public function indexAction(Request $request): Response
    {
        $data = [];
        $data['valider'] = 2;

        $search_form = $this->createForm(
            SearchAssociationType::class,
            $data
        );
        $search_form->handleRequest($request);
        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
        }

        $associations = $this->associationRepository->search($data);

        return $this->render(
            '@Volontariat/admin/association/index.html.twig',
            [
                'form' => $search_form->createView(),
                'associations' => $associations,
            ]
        );
    }

    #[Route(path: '/new', name: 'volontariat_admin_association_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request): Response
    {
        $association = new Association();
        $form = $this->createForm(AssocationType::class, $association)
            ->add('submit', SubmitType::class, ['label' => 'Create']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationRepository->persist($association);
            $this->associationRepository->flush();

            $this->fileHelper->traitementFiles($association);

            $this->addFlash('success', 'Le association a bien été ajouté');

            return $this->redirectToRoute('volontariat_admin_association_show', ['id' => $association->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/association/new.html.twig',
            [
                'association' => $association,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/show', name: 'volontariat_admin_association_show')]
    public function showAction(Association $association): Response
    {
        $images = $this->fileHelper->getImages($association);
        $dissocierForm = $this->formBuilderVolontariat->createDissocierForm($association);
        $deleteForm = $this->createDeleteForm($association);

        return $this->render(
            '@Volontariat/admin/association/show.html.twig',
            [
                'association' => $association,
                'images' => $images,
                'delete_form' => $deleteForm->createView(),
                'dissocier_form' => $dissocierForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_admin_association_edit')]
    public function editAction(Request $request, Association $association): Response
    {
        $form = $this->createForm(AssocationType::class, $association)
            ->add('submit', SubmitType::class, ['label' => 'Update']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationRepository->flush();
            $this->fileHelper->traitementFiles($association);

            $this->addFlash('success', 'Le association a bien été modifié');

            return $this->redirectToRoute('volontariat_admin_association_show', ['id' => $association->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/association/edit.html.twig',
            [
                'association' => $association,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'volontariat_admin_association_delete', methods: ['DELETE'])]
    public function deleteAction(Association $association, Request $request): RedirectResponse
    {
        $form = $this->createDeleteForm($association);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationRepository->remove($association);
            $this->associationRepository->flush();
            $this->addFlash('success', 'Le association a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_admin_association');
    }

    private function createDeleteForm(Association $association): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('volontariat_admin_association_delete', ['id' => $association->getId()])
            )
            ->setMethod(Request::METHOD_DELETE)
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }
}
