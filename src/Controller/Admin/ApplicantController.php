<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Applicant;
use AcMarche\Volontariat\Form\Admin\ApplicantNoteType;
use AcMarche\Volontariat\Form\ApplicantType;
use AcMarche\Volontariat\Repository\ApplicantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegisterController
 * @package AcMarche\Admin\Security\Controller
 * @Route("/admin/applicant")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class ApplicantController extends AbstractController
{
    /**
     * @var ApplicantRepository
     */
    private $applicantRepository;

    public function __construct(ApplicantRepository $applicantRepository)
    {
        $this->applicantRepository = $applicantRepository;
    }

    /**
     * Lists all Applicant applicants.
     *
     * @Route("/", name="volontariat_admin_applicant")
     *
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $applicants = $em->getRepository(Applicant::class)->findAll();

        return $this->render(
            '@Volontariat/admin/applicant/index.html.twig',
            array(

                'applicants' => $applicants,
            )
        );
    }

    /**
     * Displays a form to create a new Applicant applicant.
     *
     * @Route("/new/", name="volontariat_admin_applicant_new", methods={"GET","POST"})
     *
     */
    public function newAction(Request $request)
    {
        $applicant = new Applicant();
        $form = $this->createForm(ApplicantType::class, $applicant)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($applicant);
            $em->flush();
            $this->addFlash("success", "Le demandeur a bien été ajouté");

            return $this->redirectToRoute('volontariat_admin_applicant_show', ['id' => $applicant->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/applicant/new.html.twig',
            array(
                'applicant' => $applicant,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Applicant applicant.
     *
     * @Route("/{id}/show", name="volontariat_admin_applicant_show")
     *
     */
    public function showAction(Applicant $applicant)
    {
        $deleteForm = $this->createDeleteForm($applicant);

        return $this->render(
            '@Volontariat/admin/applicant/show.html.twig',
            array(
                'applicant' => $applicant,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Applicant applicant.
     *
     * @Route("/{id}/edit", name="volontariat_admin_applicant_edit")
     *
     */
    public function editAction(Request $request, Applicant $applicant)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ApplicantType::class, $applicant)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le demandeur a bien été modifié');

            return $this->redirectToRoute('volontariat_admin_applicant_show', ['id' => $applicant->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/applicant/edit.html.twig',
            array(
                'applicant' => $applicant,
                'form' => $form->createView(),
            )
        );
    }
    /**
     * Displays a form to edit an existing Applicant applicant.
     *
     * @Route("/{id}/note", name="volontariat_admin_applicant_note")
     *
     */
    public function notes(Request $request, Applicant $applicant)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ApplicantNoteType::class, $applicant)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le note a bien été modifié');

            return $this->redirectToRoute('volontariat_admin_applicant_show', ['id' => $applicant->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/applicant/edit.html.twig',
            array(
                'applicant' => $applicant,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Deletes a Applicant applicant.
     *
     * @Route("/{id}/delete", name="volontariat_admin_applicant_delete", methods={"DELETE"})
     */
    public function deleteAction(Applicant $applicant, Request $request)
    {
        $form = $this->createDeleteForm($applicant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($applicant);
            $em->flush();

            $this->addFlash('success', 'Le demandeur a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_admin_applicant');
    }

    private function createDeleteForm(Applicant $applicant)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_applicant_delete', array('id' => $applicant->getId())))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
