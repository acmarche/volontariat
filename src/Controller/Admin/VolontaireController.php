<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Admin\VolontaireNoteType;
use AcMarche\Volontariat\Form\Search\SearchVolontaireType;
use AcMarche\Volontariat\Form\Admin\VolontaireType;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Service\FileHelper;
use AcMarche\Volontariat\Service\FormBuilderVolontariat;
use AcMarche\Volontariat\Service\VolontariatConstante;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Volontaire controller.
 */
#[Route(path: '/admin/volontaire')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class VolontaireController extends AbstractController
{
    public function __construct(private VolontaireRepository $volontaireRepository, private FileHelper $fileHelper, private FormBuilderVolontariat $formBuilderVolontariat, private ManagerRegistry $managerRegistry)
    {
    }
    /**
     * Lists all Volontaire entities.
     *
     *
     *
     */
    #[Route(path: '/', name: 'volontariat_admin_volontaire')]
    public function indexAction(Request $request) : Response
    {
        $session = $request->getSession();
        $data = array();
        $data['valider'] = 2;
        $key = VolontariatConstante::VOLONTAIRE_ADMIN_SEARCH;
        if ($session->has($key)) {
            $data = unserialize($session->get($key));
        }
        $search_form = $this->createForm(SearchVolontaireType::class, $data);
        $search_form->handleRequest($request);
        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
        }
        $session->set($key, serialize($data));
        $volontaires = $this->volontaireRepository->search($data);
        return $this->render(
            '@Volontariat/admin/volontaire/index.html.twig',
            array(
                'form' => $search_form->createView(),
                'volontaires' => $volontaires,
            )
        );
    }
    /**
     * Displays a form to create a new Volontaire volontaire.
     */
    #[Route(path: '/new', name: 'volontariat_admin_volontaire_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request) : Response
    {
        $volontaire = new Volontaire();
        $form = $this->createForm(VolontaireType::class, $volontaire)
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->volontaireRepository->insert($volontaire);

            $this->fileHelper->traitementFiles($volontaire);

            $this->addFlash("success", "Le volontaire a bien été ajouté");

            return $this->redirectToRoute('volontariat_admin_volontaire_show', ['id' => $volontaire->getId()]);
        }
        return $this->render(
            '@Volontariat/admin/volontaire/new.html.twig',
            array(
                'volontaire' => $volontaire,
                'form' => $form->createView(),
            )
        );
    }
    /**
     * Finds and displays a Volontaire volontaire.
     *
     *
     */
    #[Route(path: '/{id}/show', name: 'volontariat_admin_volontaire_show')]
    public function showAction(Volontaire $volontaire) : Response
    {
        $deleteForm = $this->createDeleteForm($volontaire);
        $dissocierForm = $this->formBuilderVolontariat->createDissocierForm($volontaire);
        return $this->render(
            '@Volontariat/admin/volontaire/show.html.twig',
            array(
                'volontaire' => $volontaire,
                'delete_form' => $deleteForm->createView(),
                'dissocier_form' => $dissocierForm->createView(),
            )
        );
    }
    /**
     * Displays a form to edit an existing Volontaire volontaire.
     *
     *
     */
    #[Route(path: '/{id}/edit', name: 'volontariat_admin_volontaire_edit')]
    public function editAction(Request $request, Volontaire $volontaire) : Response
    {
        $form = $this->createForm(VolontaireType::class, $volontaire)
            ->add('submit', SubmitType::class, array('label' => 'Update'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->fileHelper->traitementFiles($volontaire);
            $this->volontaireRepository->save();

            $this->addFlash('success', 'Le volontaire a bien été modifié');

            return $this->redirectToRoute('volontariat_admin_volontaire_show', ['id' => $volontaire->getId()]);
        }
        return $this->render(
            '@Volontariat/admin/volontaire/edit.html.twig',
            array(
                'volontaire' => $volontaire,
                'form' => $form->createView(),
            )
        );
    }
    /**
     * Deletes a Volontaire volontaire.
     *
     *
     */
    #[Route(path: '/{id}/delete', name: 'volontariat_admin_volontaire_delete', methods: ['DELETE'])]
    public function deleteAction(Volontaire $volontaire, Request $request) : RedirectResponse
    {
        $form = $this->createDeleteForm($volontaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->volontaireRepository->remove($volontaire);

            $this->addFlash('success', 'Le volontaire a bien été supprimé');
        }
        return $this->redirectToRoute('volontariat_admin_volontaire');
    }
    private function createDeleteForm(Volontaire $volontaire): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_volontaire_delete', array('id' => $volontaire->getId())))
            ->setMethod(Request::METHOD_DELETE)
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
    /**
     * Displays a form to edit an existing Applicant applicant.
     *
     *
     */
    #[Route(path: '/{id}/note', name: 'volontariat_admin_volontaire_note')]
    public function notes(Request $request, Volontaire $volontaire) : Response
    {
        $em = $this->managerRegistry->getManager();
        $form = $this->createForm(VolontaireNoteType::class, $volontaire)
            ->add('submit', SubmitType::class, array('label' => 'Update'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le note a bien été modifié');

            return $this->redirectToRoute('volontariat_admin_volontaire_show', ['id' => $volontaire->getId()]);
        }
        return $this->render(
            '@Volontariat/admin/volontaire/note.html.twig',
            array(
                'volontaire' => $volontaire,
                'form' => $form->createView(),
            )
        );
    }
}
