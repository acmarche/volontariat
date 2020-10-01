<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Form\ActiviteType;
use AcMarche\Volontariat\Service\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Activite controller.
 *
 * @Route("/admin/activite")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class ActiviteController extends AbstractController
{
    /**
     * @var FileHelper
     */
    private $fileHelper;

    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    /**
     * Lists all Activite entities.
     *
     * @Route("/", name="volontariat_admin_activite")
     *
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Activite::class)->findAll();

        return $this->render(
            '@Volontariat/admin/activite/index.html.twig',
            array(
                'entities' => $entities,
            )
        );
    }

    /**
     * Displays a form to create a new Activite activite.
     *
     * @Route("/new/{id}", name="volontariat_admin_activite_new", methods={"GET","POST"})
     *
     */
    public function newAction(Request $request, Association $association)
    {
        $activite = new Activite();
        $activite->setAssociation($association);
        $activite->setValider(true);

        $form = $this->createForm(ActiviteType::class, $activite)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($activite);
            $em->flush();

            $this->addFlash("success", "L' activitée a bien été ajoutée");

            return $this->redirectToRoute('volontariat_admin_activite_show', ['id' => $activite->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/activite/new.html.twig',
            array(
                'activite' => $activite,
                'association' => $association,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Activite activite.
     *
     * @Route("/{id}/show", name="volontariat_admin_activite_show")
     *
     */
    public function showAction(Activite $activite)
    {
        $deleteForm = $this->createDeleteForm($activite);
        $images = $this->fileHelper->getImages($activite);

        return $this->render(
            '@Volontariat/admin/activite/show.html.twig',
            array(
                'activite' => $activite,
                'images' => $images,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Activite activite.
     *
     * @Route("/{id}/edit", name="volontariat_admin_activite_edit")
     *
     */
    public function editAction(Request $request, Activite $activite)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ActiviteType::class, $activite)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'L\' activitée a bien été modifiée');

            return $this->redirectToRoute('volontariat_admin_activite_show', ['id' => $activite->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/activite/edit.html.twig',
            array(
                'activite' => $activite,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Deletes a Activite activite.
     *
     * @Route("/{id}/delete", name="volontariat_admin_activite_delete", methods={"DELETE"})
     */
    public function deleteAction(Activite $activite, Request $request)
    {
        $form = $this->createDeleteForm($activite);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($activite);
            $em->flush();

            $this->addFlash('success', 'L\' activite a bien été supprimée');
        }

        return $this->redirectToRoute('volontariat_admin_activite');
    }

    private function createDeleteForm(Activite $activite)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_activite_delete', array('id' => $activite->getId())))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
