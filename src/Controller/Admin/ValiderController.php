<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Event\ActiviteEvent;
use AcMarche\Volontariat\Event\AssociationEvent;
use AcMarche\Volontariat\Form\Admin\ValiderType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Valider controller.
 *
 * @Route("/admin/valider")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class ValiderController extends AbstractController
{
    /**
     * @var AssociationRepository
     */
    private $associationRepository;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(AssociationRepository $associationRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->associationRepository = $associationRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/", name="volontariat_admin_valider", methods={"GET","POST"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
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

    /**
     * @Route("/association/{id}/edit", name="volontariat_admin_association_valider")
     *
     */
    public function associationAction(Request $request, Association $association)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ValiderType::class, $association)
            ->add('submit', SubmitType::class, array('label' => 'Valider'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'L\' association a bien été validée');

            $event = new AssociationEvent($association);
            $this->eventDispatcher->dispatch( $event,AssociationEvent::ASSOCIATION_VALIDER_FINISH);
            $this->eventDispatcher->dispatch($event,AssociationEvent::ASSOCIATION_NEW);

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

    /**
     * @Route("/activite/{id}/edit", name="volontariat_admin_activite_valider")
     *
     */
    public function activiteAction(Request $request, Activite $activite)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ValiderType::class, $activite)
            ->add('submit', SubmitType::class, array('label' => 'Valider'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'L\'activitée a bien été validée');

            $event = new ActiviteEvent($activite);
            $this->eventDispatcher->dispatch($event,ActiviteEvent::ACTIVITE_VALIDER_FINISH);
            $this->eventDispatcher->dispatch($event,ActiviteEvent::ACTIVITE_NEW);

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
