<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Form\Admin\SecteurType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/secteur')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class SecteurController extends AbstractController
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    #[Route(path: '/', name: 'volontariat_admin_secteur', methods: ['GET'])]
    public function indexAction() : Response
    {
        $em = $this->managerRegistry->getManager();
        $entities = $em->getRepository(Secteur::class)->findAll();
        return $this->render(
            '@Volontariat/admin/secteur/index.html.twig',
            array(
            'entities' => $entities,
        )
        );
    }

    #[Route(path: '/new', name: 'volontariat_admin_secteur_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request) : Response
    {
        $secteur = new Secteur();
        $form = $this->createForm(SecteurType::class, $secteur)
            ->add('Create', SubmitType::class, array('label' => 'Update'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($secteur);
            $em->flush();
            $this->addFlash("success", "Le secteur a bien été ajouté");

            return $this->redirectToRoute('volontariat_admin_secteur');
        }
        return $this->render(
            '@Volontariat/admin/secteur/new.html.twig',
            array(
            'secteur' => $secteur,
            'form' => $form->createView(),
        )
        );
    }

    #[Route(path: '/{id}', name: 'volontariat_admin_secteur_show', methods: ['GET'])]
    public function showAction(Secteur $secteur) : Response
    {
        $deleteForm = $this->createDeleteForm($secteur);
        return $this->render(
            '@Volontariat/admin/secteur/show.html.twig',
            array(
            'secteur' => $secteur,
            'delete_form' => $deleteForm->createView(),
        )
        );
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_admin_secteur_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Secteur $secteur) : Response
    {
        $em = $this->managerRegistry->getManager();
        $editForm = $this->createForm(SecteurType::class, $secteur)
            ->add('submit', SubmitType::class, array('label' => 'Update'));
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash("success", "Le secteur a bien été modifié");

            return $this->redirectToRoute('volontariat_admin_secteur');
        }
        return $this->render(
            '@Volontariat/admin/secteur/edit.html.twig',
            array(
            'secteur' => $secteur,
            'edit_form' => $editForm->createView(),
        )
        );
    }

    #[Route(path: '/{id}', name: 'volontariat_admin_secteur_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, Secteur $secteur) : RedirectResponse
    {
        $form = $this->createDeleteForm($secteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $em->remove($secteur);
            $em->flush();
            $this->addFlash("success", "Le secteur a bien été supprimé");
        }
        return $this->redirectToRoute('volontariat_admin_secteur');
    }

    private function createDeleteForm(Secteur $secteur): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_secteur_delete', array('id' => $secteur->getId())))
            ->setMethod(Request::METHOD_DELETE)
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
