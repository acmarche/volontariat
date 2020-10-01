<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Event\ActiviteEvent;
use AcMarche\Volontariat\Form\ActiviteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Activite controller.
 *
 * @Route("/backend/activite")
 * @IsGranted("ROLE_VOLONTARIAT")
 */
class ActiviteController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Lists all Activite entities.
     *
     * @Route("/index/{id}", name="volontariat_backend_activite", methods={"GET"})

     * use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;("is_granted('edit', association)")
     *
     */
    public function indexAction(Association $association)
    {
        $formDelete = $this->createDeleteForm();

        return $this->render('@Volontariat/backend/activite/index.html.twig', array(
            'activites' => $association->getActivites(),
            'association' => $association,
            'form_delete' => $formDelete->createView(),
        ));
    }

    /**
     * Displays a form to create a new Activite activite.
     *
     * @Route("/new/{id}", name="volontariat_backend_activite_new", methods={"GET","POST"})
     * @IsGranted("edit", subject="association")
     */
    public function new(Request $request, Association $association)
    {
        $activite = new Activite();
        $activite->setAssociation($association);
        $activite->setUser($this->getUser());

        $form = $this->createForm(ActiviteType::class, $activite)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($activite);
            $em->flush();

            $this->addFlash("success", "L'activitée a bien été ajoutée");
            $this->addFlash("warning", "L'activitée doit être validée par un administrateur");

            $event = new ActiviteEvent($activite);
            $this->eventDispatcher->dispatch(ActiviteEvent::ACTIVITE_VALIDER_REQUEST, $event);

            return $this->redirectToRoute('volontariat_backend_image_activite', ['id' => $activite->getId()]);
        }

        return $this->render('@Volontariat/backend/activite/new.html.twig', array(
            'activite' => $activite,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Activite activite.
     *
     * @Route("/{id}/edit", name="volontariat_backend_activite_edit", methods={"GET","POST"})
     * @IsGranted("edit", subject="activite")
     *
     */
    public function editAction(Request $request, Activite $activite)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(ActiviteType::class, $activite)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash("success", "L'activitée a bien été modifiée");

            return $this->redirectToRoute(
                'volontariat_backend_activite',
                ['id' => $activite->getAssociation()->getId()]
            );
        }

        return $this->render('@Volontariat/backend/activite/edit.html.twig', array(
            'activite' => $activite,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Activite activite.
     *
     * @Route("/", name="volontariat_backend_activite_delete", methods={"DELETE"})
     *
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $activiteId = $request->request->get('idactivite');
        $activite = $em->getRepository(Activite::class)->find($activiteId);

        if (!$activite) {
            $this->addFlash('danger', 'Activité introuvable');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        $form = $this->createDeleteForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('delete', $activite, "Vous n'avez pas accès a cette activité.");
            $association = $activite->getAssociation();

            $em = $this->getDoctrine()->getManager();
            $em->remove($activite);
            $em->flush();
            $this->addFlash("success", "L' activité a bien été supprimée");
        }

        return $this->redirectToRoute('volontariat_backend_activite', ['id' => $association->getId()]);
    }

    /**
     * Creates a form to delete a Activite activite by id.
     *
     * @param mixed $id The activite id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_backend_activite_delete'))
            ->setMethod('DELETE')
            ->getForm();
    }
}
