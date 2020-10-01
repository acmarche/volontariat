<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Temoignage;
use AcMarche\Volontariat\Form\Admin\TemoignageType;
use AcMarche\Volontariat\Repository\TemoignageRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/admin/temoignage")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 *
 */
class TemoignageController extends AbstractController
{
    /**
     * Lists all Temoignage entities.
     *
     *
     * @Route("/", name="volontariat_admin_temoignage", methods={"GET"})
     */
    public function index(TemoignageRepository $temoignageRepository): Response
    {
        $temoignages = $temoignageRepository->findAll();

        return $this->render('admin/temoignage/index.html.twig', ['temoignages' => $temoignages]);
    }

    /**
     * Creates a new Temoignage entity.
     *
     * @Route("/new", name="volontariat_admin_temoignage_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request): Response
    {
        $temoignage = new Temoignage();

        $form = $this->createForm(TemoignageType::class, $temoignage)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($temoignage);
            $em->flush();

            $this->addFlash('success', 'temoignage.created_successfully');

            return $this->redirectToRoute('volontariat_admin_temoignage');
        }

        return $this->render(
            'admin/temoignage/new.html.twig',
            [
                'temoignage' => $temoignage,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Temoignage entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="volontariat_admin_temoignage_show", methods={"GET"})
     *
     */
    public function show(Temoignage $temoignage): Response
    {
        $deleteForm = $this->createDeleteForm($temoignage);

        return $this->render(
            'admin/temoignage/show.html.twig',
            [
                'temoignage' => $temoignage,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Temoignage entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="volontariat_admin_temoignage_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Temoignage $temoignage): Response
    {
        $form = $this->createForm(TemoignageType::class, $temoignage)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'temoignage.updated_successfully');

            return $this->redirectToRoute('volontariat_admin_temoignage_show', ['id' => $temoignage->getId()]);
        }

        return $this->render(
            'admin/temoignage/edit.html.twig',
            [
                'temoignage' => $temoignage,
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * Deletes a Temoignage entity.
     *
     * @Route("/{id}/delete", name="volontariat_admin_temoignage_delete", methods={"DELETE"})
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */

    public function deleteAction(Temoignage $temoignage, Request $request)
    {
        $form = $this->createDeleteForm($temoignage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($temoignage);
            $em->flush();

            $this->addFlash('success', 'Le témoignange a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_admin_temoignage');
    }

    private function createDeleteForm(Temoignage $temoignage)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('volontariat_admin_temoignage_delete', array('id' => $temoignage->getId()))
            )
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
