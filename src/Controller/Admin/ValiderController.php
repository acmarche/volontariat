<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Event\ActiviteEvent;
use AcMarche\Volontariat\Event\AssociationEvent;
use AcMarche\Volontariat\Form\Admin\ValiderType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/valider')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ValiderController extends AbstractController
{
    public function __construct(
        private AssociationRepository $associationRepository,
        private EventDispatcherInterface $eventDispatcher,
        private ManagerRegistry $managerRegistry
    ) {
    }

    #[Route(path: '/', name: 'volontariat_admin_valider', methods: ['GET', 'POST'])]
    public function indexaction(): Response
    {
        $em = $this->managerRegistry->getManager();
        $associations = $this->associationRepository->findBy(['valider' => false]);
        $activites = $em->getRepository(Activite::class)->findBy(['valider' => false]);

        return $this->render(
            '@Volontariat/admin/valider/index.html.twig',
            array(
                'associations' => $associations,
                'activites' => $activites,
            )
        );
    }

    #[Route(path: '/association/{id}/edit', name: 'volontariat_admin_association_valider')]
    public function associationaction(Request $request, Association $association): Response
    {
        $em = $this->managerRegistry->getManager();
        $form = $this->createForm(ValiderType::class, $association)
            ->add('submit', SubmitType::class, array('label' => 'Valider'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'L\' association a bien été validée');

            $event = new AssociationEvent($association);
            $this->eventDispatcher->dispatch($event, AssociationEvent::ASSOCIATION_VALIDER_FINISH);
            $this->eventDispatcher->dispatch($event, AssociationEvent::ASSOCIATION_NEW);

            return $this->redirectToRoute('volontariat_admin_association_show', ['id' => $association->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/valider/association.html.twig',
            array(
                'association' => $association,
                'form' => $form->createView(),
            )
        );
    }

    #[Route(path: '/activite/{id}/edit', name: 'volontariat_admin_activite_valider')]
    public function activiteaction(Request $request, Activite $activite): Response
    {
        $em = $this->managerRegistry->getManager();
        $form = $this->createForm(ValiderType::class, $activite)
            ->add('submit', SubmitType::class, array('label' => 'Valider'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'L\'activitée a bien été validée');

            $event = new ActiviteEvent($activite);
            $this->eventDispatcher->dispatch($event, ActiviteEvent::ACTIVITE_VALIDER_FINISH);
            $this->eventDispatcher->dispatch($event, ActiviteEvent::ACTIVITE_NEW);

            return $this->redirectToRoute('volontariat_admin_activite_show', ['id' => $activite->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/valider/activite.html.twig',
            array(
                'activite' => $activite,
                'form' => $form->createView(),
            )
        );
    }
}
