<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Event\VolontaireEvent;
use AcMarche\Volontariat\Form\VolontairePublicType;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Service\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Volontaire controller.
 *
 * @Route("/backend/volontaire")
 * @IsGranted("ROLE_VOLONTARIAT"
 *
 */
class VolontaireController extends AbstractController
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
     * @var VolontaireRepository
     */
    private $volontaireRepository;

    public function __construct(
        VolontaireRepository $volontaireRepository,
        FileHelper $fileHelper,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->fileHelper = $fileHelper;
        $this->eventDispatcher = $eventDispatcher;
        $this->volontaireRepository = $volontaireRepository;
    }

    /**
     * @Route("/", name="volontariat_backend_volontaire_index", methods={"GET"})
     *
     */
    public function indexAction()
    {
        $volontaires = $this->volontaireRepository->findBy(['user' => $this->getUser()]);

        $formDeleteVolontaire = $this->createDeleteForm();

        return $this->render(
            'backend/volontaire/index.html.twig',
            array(
                'volontaires' => $volontaires,
                'form_delete_volontaire' => $formDeleteVolontaire->createView(),
            )
        );
    }

    /**
     * Displays a form to create a new Volontaire volontaire.
     *
     * @Route("/new", name="volontariat_backend_volontaire_new", methods={"GET","POST"})
     */
    public function newAction(Request $request)
    {
        $user = $this->getUser();
        $volontaire = new Volontaire();
        $volontaire->setUser($user);
        $volontaire->setSurname($user->getPrenom());
        $volontaire->setName($user->getNom());
        $volontaire->setEmail($user->getEmail());

        $form = $this->createForm(VolontairePublicType::class, $volontaire)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($volontaire);
            $em->flush();//ici pour avoir id

            $this->fileHelper->traitementFiles($volontaire);
            $em->flush();

            $this->addFlash("success", "Le volontaire a bien été ajouté");

            $event = new VolontaireEvent($volontaire);
            $this->eventDispatcher->dispatch(VolontaireEvent::VOLONTAIRE_NEW, $event);

            return $this->redirectToRoute('volontariat_backend_volontaire_index');
        }

        return $this->render(
            'backend/volontaire/new.html.twig',
            array(
                'volontaire' => $volontaire,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Volontaire volontaire.
     *
     * @Route("/{id}/edit", name="volontariat_backend_volontaire_edit")
     * @IsGranted("edit",  subject="volontaire")
     *
     */
    public function editAction(Request $request, Volontaire $volontaire)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(VolontairePublicType::class, $volontaire)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->fileHelper->traitementFiles($volontaire);
            $em->flush();

            $this->addFlash('success', 'Le volontaire a bien été modifié');

            return $this->redirectToRoute('volontariat_backend_volontaire_index');
        }

        return $this->render(
            'backend/volontaire/edit.html.twig',
            array(
                'volontaire' => $volontaire,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Deletes a Volontaire volontaire.
     *
     * @Route("/delete", name="volontariat_backend_volontaire_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request)
    {
        $id = intval($request->request->get('id'));
        var_dump($id);
        $volontaire = $this->volontaireRepository->find($id);

        $this->denyAccessUnlessGranted('delete', $volontaire, "Vous n'avez pas accès.");

        $form = $this->createDeleteForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($volontaire);
            $em->flush();

            $this->addFlash('success', 'Le volontaire a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_backend_volontaire_index');
    }

    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('volontariat_backend_volontaire_delete')
            )
            ->setMethod('DELETE')
            ->getForm();
    }
}
