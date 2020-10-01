<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Form\Admin\BesoinType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Besoin controller.
 *
 * @Route("/admin/besoin")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class BesoinController extends AbstractController
{
    /**
     * Lists all Besoin entities.
     *
     * @Route("/", name="volontariat_admin_besoin")
     *
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Besoin::class)->findAll();

        return $this->render('admin/besoin/index.html.twig', array(

            'entities' => $entities,
        ));
    }

    /**
     * Displays a form to create a new Besoin besoin.
     *
     * @Route("/new/{id}", name="volontariat_admin_besoin_new", methods={"GET","POST"})
     *
     */
    public function newAction(Request $request, Association $association)
    {
        $besoin = new Besoin();
        $besoin->setAssociation($association);
        $form = $this->createForm(BesoinType::class, $besoin)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($besoin);
            $em->flush();
            $this->addFlash("success", "Le besoin a bien été ajouté");

            return $this->redirectToRoute('volontariat_admin_besoin_show', ['id' => $besoin->getId()]);
        }

        return $this->render('admin/besoin/new.html.twig', array(
            'besoin' => $besoin,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Besoin besoin.
     *
     * @Route("/{id}/show", name="volontariat_admin_besoin_show")
     *
     */
    public function showAction(Besoin $besoin)
    {
        $deleteForm = $this->createDeleteForm($besoin);

        return $this->render('admin/besoin/show.html.twig', array(
            'besoin' => $besoin,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Besoin besoin.
     *
     * @Route("/{id}/edit", name="volontariat_admin_besoin_edit")
     *
     */
    public function editAction(Request $request, Besoin $besoin)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(BesoinType::class, $besoin)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le besoin a bien été modifié');

            return $this->redirectToRoute('volontariat_admin_besoin_show', ['id' => $besoin->getId()]);
        }

        return $this->render('admin/besoin/edit.html.twig', array(
            'besoin' => $besoin,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Besoin besoin.
     *
     * @Route("/{id}/delete", name="volontariat_admin_besoin_delete", methods={"DELETE"})
     */
    public function deleteAction(Besoin $besoin, Request $request)
    {
        $form = $this->createDeleteForm($besoin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($besoin);
            $em->flush();

            $this->addFlash('success', 'Le besoin a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_admin_besoin');
    }

    private function createDeleteForm(Besoin $besoin)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_besoin_delete', array('id' => $besoin->getId())))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
