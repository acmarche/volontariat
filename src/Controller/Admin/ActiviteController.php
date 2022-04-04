<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Form\ActiviteType;
use AcMarche\Volontariat\Service\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Activite controller.
 */
#[Route(path: '/admin/activite')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ActiviteController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper, private ManagerRegistry $managerRegistry)
    {
    }
    /**
     * Lists all Activite entities.
     *
     *
     *
     */
    #[Route(path: '/', name: 'volontariat_admin_activite')]
    public function indexAction() : Response
    {
        $em = $this->managerRegistry->getManager();
        $entities = $em->getRepository(Activite::class)->findAll();
        return $this->render(
            '@Volontariat/admin/activite/index.html.twig',
            array(
                'entities' => $entities,
            )
        );
    }
    /**
     * Displays a form to create a new Activite activite.
     *
     *
     */
    #[Route(path: '/new/{id}', name: 'volontariat_admin_activite_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request, Association $association) : Response
    {
        $activite = new Activite();
        $activite->setAssociation($association);
        $activite->setValider(true);
        $form = $this->createForm(ActiviteType::class, $activite)
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($activite);
            $em->flush();

            $this->addFlash("success", "L' activitée a bien été ajoutée");

            return $this->redirectToRoute('volontariat_admin_activite_show', ['id' => $activite->getId()]);
        }
        return $this->render(
            '@Volontariat/admin/activite/new.html.twig',
            array(
                'activite' => $activite,
                'association' => $association,
                'form' => $form->createView(),
            )
        );
    }
    /**
     * Finds and displays a Activite activite.
     *
     *
     */
    #[Route(path: '/{id}/show', name: 'volontariat_admin_activite_show')]
    public function showAction(Activite $activite) : Response
    {
        $deleteForm = $this->createDeleteForm($activite);
        $images = $this->fileHelper->getImages($activite);
        return $this->render(
            '@Volontariat/admin/activite/show.html.twig',
            array(
                'activite' => $activite,
                'images' => $images,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }
    /**
     * Displays a form to edit an existing Activite activite.
     *
     *
     */
    #[Route(path: '/{id}/edit', name: 'volontariat_admin_activite_edit')]
    public function editAction(Request $request, Activite $activite) : Response
    {
        $em = $this->managerRegistry->getManager();
        $form = $this->createForm(ActiviteType::class, $activite)
            ->add('submit', SubmitType::class, array('label' => 'Update'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'L\' activitée a bien été modifiée');

            return $this->redirectToRoute('volontariat_admin_activite_show', ['id' => $activite->getId()]);
        }
        return $this->render(
            '@Volontariat/admin/activite/edit.html.twig',
            array(
                'activite' => $activite,
                'form' => $form->createView(),
            )
        );
    }
    /**
     * Deletes a Activite activite.
     */
    #[Route(path: '/{id}/delete', name: 'volontariat_admin_activite_delete', methods: ['DELETE'])]
    public function deleteAction(Activite $activite, Request $request) : RedirectResponse
    {
        $form = $this->createDeleteForm($activite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $em->remove($activite);
            $em->flush();

            $this->addFlash('success', 'L\' activite a bien été supprimée');
        }
        return $this->redirectToRoute('volontariat_admin_activite');
    }
    private function createDeleteForm(Activite $activite): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_activite_delete', array('id' => $activite->getId())))
            ->setMethod(Request::METHOD_DELETE)
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
