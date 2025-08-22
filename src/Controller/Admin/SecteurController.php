<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Form\Admin\SecteurType;
use AcMarche\Volontariat\Repository\SecteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class SecteurController extends AbstractController
{
    public function __construct(private SecteurRepository $secteurRepository)
    {
    }

    #[Route(path: '/admin/secteur/', name: 'volontariat_admin_secteur', methods: ['GET'])]
    public function index(): Response
    {
        $secteurs = $this->secteurRepository->findAllOrdered();

        return $this->render(
            '@Volontariat/admin/secteur/index.html.twig',
            [
                'secteurs' => $secteurs,
            ]
        );
    }

    #[Route(path: '/admin/secteur/new', name: 'volontariat_admin_secteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $secteur = new Secteur();
        $form = $this->createForm(SecteurType::class, $secteur);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->secteurRepository->persist($secteur);
            $this->secteurRepository->flush();
            $this->addFlash('success', 'Le secteur a bien été ajouté');

            return $this->redirectToRoute('volontariat_admin_secteur');
        }

        return $this->render(
            '@Volontariat/admin/secteur/new.html.twig',
            [
                'secteur' => $secteur,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/admin/secteur/{id}', name: 'volontariat_admin_secteur_show', methods: ['GET'])]
    public function show(Secteur $secteur): Response
    {
        return $this->render(
            '@Volontariat/admin/secteur/show.html.twig',
            [
                'secteur' => $secteur,
            ]
        );
    }

    #[Route(path: '/admin/secteur/{id}/edit', name: 'volontariat_admin_secteur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Secteur $secteur): Response
    {
        $editForm = $this->createForm(SecteurType::class, $secteur);

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->secteurRepository->flush();
            $this->addFlash('success', 'Le secteur a bien été modifié');

            return $this->redirectToRoute('volontariat_admin_secteur');
        }

        return $this->render(
            '@Volontariat/admin/secteur/edit.html.twig',
            [
                'secteur' => $secteur,
                'form' =>  $editForm,
            ]
        );
    }

    #[Route(path: '/admin/secteur/{id}/delete', name: 'volontariat_admin_secteur_delete', methods: ['POST'])]
    public function delete(Request $request, Secteur $secteur): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$secteur->getId(), $request->request->get('_token'))) {
            $this->secteurRepository->remove($secteur);
            $this->secteurRepository->flush();
            $this->addFlash('success', 'Le secteur a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_admin_secteur');
    }
}
