<?php

namespace AcMarche\Volontariat\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
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
 *
 */
#[Route(path: '/backend/volontaire')]
#[IsGranted('ROLE_VOLONTARIAT')]
class VolontaireController extends AbstractController
{
    public function __construct(private VolontaireRepository $volontaireRepository, private FileHelper $fileHelper, private EventDispatcherInterface $eventDispatcher, private ManagerRegistry $managerRegistry)
    {
    }
    #[Route(path: '/', name: 'volontariat_backend_volontaire_index', methods: ['GET'])]
    public function indexAction() : Response
    {
        $volontaires = $this->volontaireRepository->findBy(['user' => $this->getUser()]);
        $formDeleteVolontaire = $this->createDeleteForm();
        return $this->render(
            '@Volontariat/backend/volontaire/index.html.twig',
            array(
                'volontaires' => $volontaires,
                'form_delete_volontaire' => $formDeleteVolontaire->createView(),
            )
        );
    }
    /**
     * Displays a form to create a new Volontaire volontaire.
     */
    #[Route(path: '/new', name: 'volontariat_backend_volontaire_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request) : Response
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
            $em = $this->managerRegistry->getManager();
            $em->persist($volontaire);
            $em->flush();//ici pour avoir id

            $this->fileHelper->traitementFiles($volontaire);
            $em->flush();

            $this->addFlash("success", "Le volontaire a bien été ajouté");

            $event = new VolontaireEvent($volontaire);
            $this->eventDispatcher->dispatch($event, VolontaireEvent::VOLONTAIRE_NEW);

            return $this->redirectToRoute('volontariat_backend_volontaire_index');
        }
        return $this->render(
            '@Volontariat/backend/volontaire/new.html.twig',
            array(
                'volontaire' => $volontaire,
                'form' => $form->createView(),
            )
        );
    }
    /**
     * Displays a form to edit an existing Volontaire volontaire.
     *
     *
     */
    #[Route(path: '/{id}/edit', name: 'volontariat_backend_volontaire_edit')]
    #[IsGranted('edit', subject: 'volontaire')]
    public function editAction(Request $request, Volontaire $volontaire) : Response
    {
        $em = $this->managerRegistry->getManager();
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
            '@Volontariat/backend/volontaire/edit.html.twig',
            array(
                'volontaire' => $volontaire,
                'form' => $form->createView(),
            )
        );
    }
    /**
     * Deletes a Volontaire volontaire.
     */
    #[Route(path: '/delete', name: 'volontariat_backend_volontaire_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request) : RedirectResponse
    {
        $id = (int) $request->request->get('id');
        $volontaire = $this->volontaireRepository->find($id);
        $this->denyAccessUnlessGranted('delete', $volontaire, "Vous n'avez pas accès.");
        $form = $this->createDeleteForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $em->remove($volontaire);
            $em->flush();

            $this->addFlash('success', 'Le volontaire a bien été supprimé');
        }
        return $this->redirectToRoute('volontariat_backend_volontaire_index');
    }
    private function createDeleteForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('volontariat_backend_volontaire_delete')
            )
            ->setMethod(Request::METHOD_DELETE)
            ->getForm();
    }
}
