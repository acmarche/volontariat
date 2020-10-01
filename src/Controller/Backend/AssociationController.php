<?php

namespace AcMarche\Volontariat\Controller\Backend;

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

/**
 * Association controller.
 *
 * @Route("/backend/association")
 * @IsGranted("ROLE_VOLONTARIAT")
 */
class AssociationController extends AbstractController
{
    /**
     * @var FileHelper
     */
    private $fileHelper;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var AssociationRepository
     */
    private $associationRepository;

    public function __construct(
        AssociationRepository $associationRepository,
        FileHelper $fileHelper,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->fileHelper = $fileHelper;
        $this->eventDispatcher = $eventDispatcher;
        $this->associationRepository = $associationRepository;
    }

    /**
     * Lists all Association entities.
     *
     * @Route("/", name="volontariat_backend_association_index", methods={"GET"})
     *
     */
    public function indexAction()
    {
        $associations = $this->associationRepository->findBy(['user' => $this->getUser()]);
        $formDeleteAssociation = $this->createDeleteForm();

        return $this->render(
            'backend/association/index.html.twig',
            array(
                'associations' => $associations,
                'form_delete_association' => $formDeleteAssociation->createView(),
            )
        );
    }

    /**
     * Displays a form to create a new Association association.
     *
     * @Route("/new", name="volontariat_backend_association_new", methods={"GET","POST"})
     *
     */
    public function newAction(Request $request)
    {
        $user = $this->getUser();
        $association = new Association();
        $association->setUser($user);

        $form = $this->createForm(AssociationPublicType::class, $association)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $association->setValider(false);
            $em->persist($association);
            $em->flush();//pour getId

            $this->fileHelper->traitementFiles($association);

            $em->flush();

            $this->addFlash("success", "L' association a bien été ajoutée");
            $this->addFlash("warning", "L' association doit être validée par un administrateur");

            $event = new AssociationEvent($association);
            $this->eventDispatcher->dispatch(AssociationEvent::ASSOCIATION_VALIDER_REQUEST, $event);

            return $this->redirectToRoute('volontariat_backend_association_index');
        }

        return $this->render(
            'backend/association/new.html.twig',
            array(
                'association' => $association,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Association association.
     *
     * @Route("/{id}/edit", name="volontariat_backend_association_edit")
     * use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;("is_granted('edit', association)")
     *
     */
    public function editAction(Request $request, Association $association)
    {
        $em = $this->getDoctrine()->getManager();

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
            'backend/association/edit.html.twig',
            array(
                'association' => $association,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Deletes a Association association.
     *
     * @Route("/delete", name="volontariat_backend_association_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request)
    {
        $id = intval($request->request->get('associationid'));

        $association = $this->associationRepository->find($id);

        $this->denyAccessUnlessGranted('delete', $association, "Vous n'avez pas accès.");

        $form = $this->createDeleteForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($association);
            $em->flush();

            $this->addFlash('success', 'L\' association a bien été supprimée');
        }

        return $this->redirectToRoute('volontariat_backend_association_index');
    }

    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('volontariat_backend_association_delete')
            )
            ->setMethod('DELETE')
            ->getForm();
    }
}
