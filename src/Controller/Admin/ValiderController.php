<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Event\AssociationEvent;
use AcMarche\Volontariat\Form\Admin\ValiderType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/valider')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ValiderController extends AbstractController
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ManagerRegistry $managerRegistry
    ) {
    }

    #[Route(path: '/association/{id}/edit', name: 'volontariat_admin_association_valider')]
    public function association(Request $request, Association $association): Response
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


}
