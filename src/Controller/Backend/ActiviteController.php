<?php

namespace AcMarche\Volontariat\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
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

#[Route(path: '/backend/activite')]
#[IsGranted('ROLE_VOLONTARIAT')]
class ActiviteController extends AbstractController
{
    public function __construct(private EventDispatcherInterface $eventDispatcher, private ManagerRegistry $managerRegistry)
    {
    }

    #[Route(path: '/index/{id}', name: 'volontariat_backend_activite', methods: ['GET'])]
    public function indexAction(Association $association) : Response
    {
        $formDelete = $this->createDeleteForm();
        return $this->render('@Volontariat/backend/activite/index.html.twig', array(
            'activites' => $association->getActivites(),
            'association' => $association,
            'form_delete' => $formDelete->createView(),
        ));
    }

    #[Route(path: '/new/{id}', name: 'volontariat_backend_activite_new', methods: ['GET', 'POST'])]
    #[IsGranted('edit', subject: 'association')]
    public function new(Request $request, Association $association) : Response
    {
        $activite = new Activite();
        $activite->setAssociation($association);
        $activite->setUser($this->getUser());
        $form = $this->createForm(ActiviteType::class, $activite)
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $em->persist($activite);
            $em->flush();

            $this->addFlash("success", "L'activitée a bien été ajoutée");
            $this->addFlash("warning", "L'activitée doit être validée par un administrateur");

            $event = new ActiviteEvent($activite);
            $this->eventDispatcher->dispatch($event, ActiviteEvent::ACTIVITE_VALIDER_REQUEST);

            return $this->redirectToRoute('volontariat_backend_image_activite', ['id' => $activite->getId()]);
        }
        return $this->render('@Volontariat/backend/activite/new.html.twig', array(
            'activite' => $activite,
            'form' => $form->createView(),
        ));
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_backend_activite_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', subject: 'activite')]
    public function editAction(Request $request, Activite $activite) : Response
    {
        $em = $this->managerRegistry->getManager();
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

    #[Route(path: '/', name: 'volontariat_backend_activite_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request) : RedirectResponse
    {
        $association = null;
        $em = $this->managerRegistry->getManager();
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

            $em = $this->managerRegistry->getManager();
            $em->remove($activite);
            $em->flush();
            $this->addFlash("success", "L' activité a bien été supprimée");
        }
        return $this->redirectToRoute('volontariat_backend_activite', ['id' => $association->getId()]);
    }

    private function createDeleteForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_backend_activite_delete'))
            ->setMethod(Request::METHOD_DELETE)
            ->getForm();
    }
}
