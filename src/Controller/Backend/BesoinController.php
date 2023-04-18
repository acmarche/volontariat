<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Form\Admin\BesoinType;
use AcMarche\Volontariat\Repository\BesoinRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/backend/besoin')]
#[IsGranted('ROLE_VOLONTARIAT')]
class BesoinController extends AbstractController
{
    use getAssociationTrait;

    public function __construct(private BesoinRepository $besoinRepository)
    {
    }

    #[Route(path: '/index', name: 'volontariat_backend_besoin', methods: ['GET'])]
    public function index(): Response
    {
        if (($hasAssociation = $this->hasAssociation()) !== null) {
            return $hasAssociation;
        }
        $annonces = $this->besoinRepository->findByAssociation($this->association);

        return $this->render(
            '@Volontariat/backend/besoin/index.html.twig',
            [
                'association' => $this->association,
                'annonces' => $annonces,
            ]
        );
    }

    #[Route(path: '/new', name: 'volontariat_backend_besoin_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if (($hasAssociation = $this->hasAssociation()) !== null) {
            return $hasAssociation;
        }
        $besoin = new Besoin();
        $besoin->setAssociation($this->association);
        if (!$this->association->valider) {
            $this->addFlash('danger', 'Votre association doit être validée avant.');

            return $this->redirectToRoute('volontariat_dashboard');
        }
        $form = $this->createForm(BesoinType::class, $besoin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->besoinRepository->persist($besoin);
            $this->besoinRepository->flush();
            $this->addFlash('success', 'Le besoin a bien été ajouté');

            return $this->redirectToRoute('volontariat_backend_besoin', ['id' => $this->association->getId()]);
        }

        return $this->render(
            '@Volontariat/backend/besoin/new.html.twig',
            [
                'besoin' => $besoin,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_backend_besoin_edit')]
    #[IsGranted('edit', subject: 'besoin')]
    public function edit(Request $request, Besoin $besoin): Response
    {
        $form = $this->createForm(BesoinType::class, $besoin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->besoinRepository->flush();

            $this->addFlash('success', 'Le besoin a bien été modifié');
            $association = $besoin->getAssociation();

            return $this->redirectToRoute('volontariat_backend_besoin', ['id' => $association->getId()]);
        }

        return $this->render(
            '@Volontariat/backend/besoin/edit.html.twig',
            [
                'besoin' => $besoin,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'volontariat_backend_besoin_delete', methods: ['POST'])]
    public function delete(Request $request, Besoin $besoin): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$besoin->getId(), $request->request->get('_token'))) {
            $this->besoinRepository->remove($besoin);
            $this->besoinRepository->flush();
            $this->addFlash('success', 'Le besoin a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_dashboard');
    }
}
