<?php

namespace AcMarche\Volontariat\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Event\AssociationEvent;
use AcMarche\Volontariat\Form\AssociationPublicType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Service\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

#[Route(path: '/backend/association')]
#[IsGranted('ROLE_VOLONTARIAT')]
class AssociationController extends AbstractController
{
    public function __construct(private AssociationRepository $associationRepository, private FileHelper $fileHelper, private EventDispatcherInterface $eventDispatcher, private ManagerRegistry $managerRegistry)
    {
    }

    #[Route(path: '/', name: 'volontariat_backend_association_index', methods: ['GET'])]
    public function indexAction() : Response
    {
        $associations = $this->associationRepository->findBy(['user' => $this->getUser()]);
        $formDeleteAssociation = $this->createDeleteForm();
        return $this->render(
            '@Volontariat/backend/association/index.html.twig',
            array(
                'associations' => $associations,
                'form_delete_association' => $formDeleteAssociation->createView(),
            )
        );
    }

    #[Route(path: '/new', name: 'volontariat_backend_association_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request) : Response
    {
        $user = $this->getUser();
        $association = new Association();
        $association->setUser($user);
        $form = $this->createForm(AssociationPublicType::class, $association)
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $association->setValider(false);
            $em->persist($association);
            $em->flush();//pour getId

            $this->fileHelper->traitementFiles($association);

            $em->flush();

            $this->addFlash("success", "L' association a bien été ajoutée");
            $this->addFlash("warning", "L' association doit être validée par un administrateur");

            $event = new AssociationEvent($association);
            $this->eventDispatcher->dispatch($event,AssociationEvent::ASSOCIATION_VALIDER_REQUEST);

            return $this->redirectToRoute('volontariat_backend_association_index');
        }
        return $this->render(
            '@Volontariat/backend/association/new.html.twig',
            array(
                'association' => $association,
                'form' => $form->createView(),
            )
        );
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_backend_association_edit')]
    public function editAction(Request $request, Association $association) : Response
    {
        $em = $this->managerRegistry->getManager();
        $form = $this->createForm(AssociationPublicType::class, $association)
            ->add('submit', SubmitType::class, array('label' => 'Update'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->fileHelper->traitementFiles($association);
            $em->flush();

            $this->addFlash('success', 'L\' association a bien été modifiée');

            return $this->redirectToRoute('volontariat_backend_association_index');
        }
        return $this->render(
            '@Volontariat/backend/association/edit.html.twig',
            array(
                'association' => $association,
                'form' => $form->createView(),
            )
        );
    }

    #[Route(path: '/delete', name: 'volontariat_backend_association_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request) : RedirectResponse
    {
        $id = (int) $request->request->get('associationid');
        $association = $this->associationRepository->find($id);
        $this->denyAccessUnlessGranted('delete', $association, "Vous n'avez pas accès.");
        $form = $this->createDeleteForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $em->remove($association);
            $em->flush();

            $this->addFlash('success', 'L\' association a bien été supprimée');
        }
        return $this->redirectToRoute('volontariat_backend_association_index');
    }
    private function createDeleteForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('volontariat_backend_association_delete')
            )
            ->setMethod(Request::METHOD_DELETE)
            ->getForm();
    }
}
