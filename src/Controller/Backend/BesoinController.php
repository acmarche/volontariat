<?php

namespace AcMarche\Volontariat\Controller\Backend;

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
 * @Route("/backend/besoin")
 * @IsGranted("ROLE_VOLONTARIAT")
 */
class BesoinController extends AbstractController
{
    /**
     * @Route("/index/{id}", name="volontariat_backend_besoin", methods={"GET"})
     * @IsGranted("edit",subject="association")
     */
    public function index(Association $association)
    {
        $formDelete = $this->createDeleteForm();

        return $this->render(
            '@Volontariat/backend/besoin/index.html.twig',
            [
                'association' => $association,
                'besoins' => $association->getBesoins(),
                'form_delete' => $formDelete->createView(),
            ]
        );
    }

    /**
     * Displays a form to create a new Besoin besoin.
     *
     * @Route("/new/{id}", name="volontariat_backend_besoin_new", methods={"GET","POST"})
     * @IsGranted("edit", subject="association")
     */
    public function newAction(Request $request, Association $association)
    {
        $besoin = new Besoin();
        $besoin->setAssociation($association);

        if (!$association->getValider()) {
            $this->addFlash('danger', 'Votre association doit être validée avant.');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        $form = $this->createForm(BesoinType::class, $besoin)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($besoin);
            $em->flush();
            $this->addFlash("success", "Le besoin a bien été ajouté");

            return $this->redirectToRoute('volontariat_backend_besoin', ['id' => $association->getId()]);
        }

        return $this->render(
            '@Volontariat/backend/besoin/new.html.twig',
            array(
                'besoin' => $besoin,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Besoin besoin.
     *
     * @Route("/{id}/edit", name="volontariat_backend_besoin_edit")
     * @IsGranted("edit", subject="besoin")
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
            $association = $besoin->getAssociation();

            return $this->redirectToRoute('volontariat_backend_besoin', ['id' => $association->getId()]);
        }

        return $this->render(
            '@Volontariat/backend/besoin/edit.html.twig',
            array(
                'besoin' => $besoin,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Deletes a Besoin besoin.
     *
     * @Route("/delete", name="volontariat_backend_besoin_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $besoinId = $request->request->get('idbesoin');
        $besoin = $em->getRepository(Besoin::class)->find($besoinId);

        if (!$besoin) {
            $this->addFlash('danger', 'Besoin introuvable');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        $form = $this->createDeleteForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('delete', $besoin, "Vous n'avez pas accès a ce besoin.");

            $em = $this->getDoctrine()->getManager();
            $association = $besoin->getAssociation();

            $em->remove($besoin);
            $em->flush();

            $this->addFlash('success', 'Le besoin a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_backend_besoin', ['id' => $association->getId()]);
    }

    /**
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_backend_besoin_delete'))
            ->setMethod('DELETE')
            ->getForm();
    }
}
