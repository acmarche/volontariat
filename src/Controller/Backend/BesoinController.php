<?php

namespace AcMarche\Volontariat\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Form\Admin\BesoinType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

#[Route(path: '/backend/besoin')]
#[IsGranted('ROLE_VOLONTARIAT')]
class BesoinController extends AbstractController
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }
    #[Route(path: '/index/{id}', name: 'volontariat_backend_besoin', methods: ['GET'])]
    #[IsGranted('edit', subject: 'association')]
    public function index(Association $association) : Response
    {
        $formDelete = $this->createDeleteForm();
        return $this->render(
            '@Volontariat/backend/besoin/index.html.twig',
            [
                'association' => $association,
                'besoins' => $association->getBesoins(),
                'form_delete' => $formDelete->createView(),
            ]
        );
    }

    #[Route(path: '/new/{id}', name: 'volontariat_backend_besoin_new', methods: ['GET', 'POST'])]
    #[IsGranted('edit', subject: 'association')]
    public function newAction(Request $request, Association $association) : Response
    {
        $besoin = new Besoin();
        $besoin->setAssociation($association);
        if (!$association->getValider()) {
            $this->addFlash('danger', 'Votre association doit être validée avant.');

            return $this->redirectToRoute('volontariat_dashboard');
        }
        $form = $this->createForm(BesoinType::class, $besoin)
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($besoin);
            $em->flush();
            $this->addFlash("success", "Le besoin a bien été ajouté");

            return $this->redirectToRoute('volontariat_backend_besoin', ['id' => $association->getId()]);
        }
        return $this->render(
            '@Volontariat/backend/besoin/new.html.twig',
            array(
                'besoin' => $besoin,
                'form' => $form->createView(),
            )
        );
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_backend_besoin_edit')]
    #[IsGranted('edit', subject: 'besoin')]
    public function editAction(Request $request, Besoin $besoin) : Response
    {
        $em = $this->managerRegistry->getManager();
        $form = $this->createForm(BesoinType::class, $besoin)
            ->add('submit', SubmitType::class, array('label' => 'Update'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le besoin a bien été modifié');
            $association = $besoin->getAssociation();

            return $this->redirectToRoute('volontariat_backend_besoin', ['id' => $association->getId()]);
        }
        return $this->render(
            '@Volontariat/backend/besoin/edit.html.twig',
            array(
                'besoin' => $besoin,
                'form' => $form->createView(),
            )
        );
    }

    #[Route(path: '/delete', name: 'volontariat_backend_besoin_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request) : RedirectResponse
    {
        $association = null;
        $em = $this->managerRegistry->getManager();
        $besoinId = $request->request->get('idbesoin');
        $besoin = $em->getRepository(Besoin::class)->find($besoinId);
        if (!$besoin) {
            $this->addFlash('danger', 'Besoin introuvable');

            return $this->redirectToRoute('volontariat_dashboard');
        }
        $form = $this->createDeleteForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('delete', $besoin, "Vous n'avez pas accès a ce besoin.");

            $em = $this->managerRegistry->getManager();
            $association = $besoin->getAssociation();

            $em->remove($besoin);
            $em->flush();

            $this->addFlash('success', 'Le besoin a bien été supprimé');
        }
        return $this->redirectToRoute('volontariat_backend_besoin', ['id' => $association->getId()]);
    }
    private function createDeleteForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_backend_besoin_delete'))
            ->setMethod(Request::METHOD_DELETE)
            ->getForm();
    }
}
