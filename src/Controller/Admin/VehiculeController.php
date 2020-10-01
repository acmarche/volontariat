<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Vehicule;
use AcMarche\Volontariat\Form\Admin\VehiculeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Vehicule controller.
 *
 * @Route("/admin/vehicule")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class VehiculeController extends AbstractController
{

    /**
     * Lists all Vehicule entities.
     *
     * @Route("/", name="volontariat_admin_vehicule", methods={"GET"})
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Vehicule::class)->findAll();

        return $this->render(
            'admin/vehicule/index.html.twig',
            array(
            'entities' => $entities,
        )
        );
    }

    /**
     * Displays a form to create a new Vehicule vehicule.
     *
     * @Route("/new", name="volontariat_admin_vehicule_new", methods={"GET","POST"})
     *
     */
    public function newAction(Request $request)
    {
        $vehicule = new Vehicule();

        $form = $this->createForm(VehiculeType::class, $vehicule)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vehicule);
            $em->flush();
            $this->addFlash("success", "Le véhicule a bien été ajouté");

            return $this->redirectToRoute('volontariat_admin_vehicule');
        }

        return $this->render(
            'admin/vehicule/new.html.twig',
            array(
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        )
        );
    }

    /**
     * Finds and displays a Vehicule vehicule.
     *
     * @Route("/{id}", name="volontariat_admin_vehicule_show", methods={"GET"})
     *
     */
    public function showAction(Vehicule $vehicule)
    {
        $deleteForm = $this->createDeleteForm($vehicule);

        return $this->render(
            'admin/vehicule/show.html.twig',
            array(
            'vehicule' => $vehicule,
            'delete_form' => $deleteForm->createView(),
        )
        );
    }

    /**
     * Displays a form to edit an existing Vehicule vehicule.
     *
     * @Route("/{id}/edit", name="volontariat_admin_vehicule_edit", methods={"GET","POST"})
     *
     */
    public function editAction(Request $request, Vehicule $vehicule)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(VehiculeType::class, $vehicule)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash("success", "Le véhicule a bien été modifié");

            return $this->redirectToRoute('volontariat_admin_vehicule');
        }

        return $this->render(
            'admin/vehicule/edit.html.twig',
            array(
            'vehicule' => $vehicule,
            'edit_form' => $editForm->createView(),
        )
        );
    }

    /**
     * Deletes a Vehicule vehicule.
     *
     * @Route("/{id}", name="volontariat_admin_vehicule_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Vehicule $vehicule)
    {
        $form = $this->createDeleteForm($vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($vehicule);
            $em->flush();
            $this->addFlash("success", "Le véhicule a bien été supprimé");
        }

        return $this->redirectToRoute('volontariat_admin_vehicule');
    }

    /**
     * Creates a form to delete a Vehicule vehicule by id.
     *
     * @param mixed $id The vehicule id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Vehicule $vehicule)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_vehicule_delete', array('id' => $vehicule)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
