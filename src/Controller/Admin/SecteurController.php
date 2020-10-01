<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Form\Admin\SecteurType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Secteur controller.
 *
 * @Route("/admin/secteur")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class SecteurController extends AbstractController
{
    /**
     * Lists all Secteur entities.
     *
     * @Route("/", name="volontariat_admin_secteur", methods={"GET"})
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Secteur::class)->findAll();

        return $this->render(
            '@Volontariat/admin/secteur/index.html.twig',
            array(
            'entities' => $entities,
        )
        );
    }

    /**
     * Displays a form to create a new Secteur secteur.
     *
     * @Route("/new", name="volontariat_admin_secteur_new", methods={"GET","POST"})
     *
     */
    public function newAction(Request $request)
    {
        $secteur = new Secteur();
        $form = $this->createForm(SecteurType::class, $secteur)
            ->add('Create', SubmitType::class, array('label' => 'Update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
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

    /**
     * Finds and displays a Secteur secteur.
     *
     * @Route("/{id}", name="volontariat_admin_secteur_show", methods={"GET"})
     *
     */
    public function showAction(Secteur $secteur)
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

    /**
     * Displays a form to edit an existing Secteur secteur.
     *
     * @Route("/{id}/edit", name="volontariat_admin_secteur_edit", methods={"GET","POST"})
     *
     */
    public function editAction(Request $request, Secteur $secteur)
    {
        $em = $this->getDoctrine()->getManager();

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

    /**
     * Deletes a Secteur secteur.
     *
     * @Route("/{id}", name="volontariat_admin_secteur_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Secteur $secteur)
    {
        $form = $this->createDeleteForm($secteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($secteur);
            $em->flush();
            $this->addFlash("success", "Le secteur a bien été supprimé");
        }

        return $this->redirectToRoute('volontariat_admin_secteur');
    }

    /**
     * Creates a form to delete a Secteur secteur by id.
     *
     * @param mixed $id The secteur id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Secteur $secteur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_secteur_delete', array('id' => $secteur->getId())))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
