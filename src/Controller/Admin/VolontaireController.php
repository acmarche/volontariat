<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Admin\VolontaireType;
use AcMarche\Volontariat\Form\Search\SearchVolontaireType;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/volontaire')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class VolontaireController extends AbstractController
{
    public function __construct(
        private VolontaireRepository $volontaireRepository,
    ) {}

    #[Route(path: '/', name: 'volontariat_admin_volontaire')]
    public function index(Request $request): Response
    {
        $data = [];
        $data['valider'] = 2;

        $search_form = $this->createForm(SearchVolontaireType::class, $data);
        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
        }
        $volontaires = $this->volontaireRepository->search($data);

        $response = new Response(null, $search_form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@Volontariat/admin/volontaire/index.html.twig',
            [
                'form' => $search_form,
                'volontaires' => $volontaires,
            ]
            , $response,
        );
    }

    #[Route(path: '/new', name: 'volontariat_admin_volontaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $volontaire = new Volontaire();
        $form = $this->createForm(VolontaireType::class, $volontaire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $volontaire->setUuid($volontaire->generateUuid());
            $this->volontaireRepository->insert($volontaire);

            $this->addFlash('success', 'Le volontaire a bien été ajouté');

            return $this->redirectToRoute('volontariat_admin_volontaire_show', ['id' => $volontaire->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/volontaire/new.html.twig',
            [
                'volontaire' => $volontaire,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}/show', name: 'volontariat_admin_volontaire_show')]
    public function show(Volontaire $volontaire): Response
    {
        return $this->render(
            '@Volontariat/admin/volontaire/show.html.twig',
            [
                'volontaire' => $volontaire,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_admin_volontaire_edit')]
    public function edit(Request $request, Volontaire $volontaire): Response
    {
        $form = $this->createForm(VolontaireType::class, $volontaire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->volontaireRepository->flush();

            $this->addFlash('success', 'Le volontaire a bien été modifié');

            return $this->redirectToRoute('volontariat_admin_volontaire_show', ['id' => $volontaire->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/volontaire/edit.html.twig',
            [
                'volontaire' => $volontaire,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'volontariat_admin_volontaire_delete', methods: ['POST'])]
    public function delete(Request $request, Volontaire $volontaire): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$volontaire->getId(), $request->request->get('_token'))) {
            $this->volontaireRepository->remove($volontaire);
            $this->volontaireRepository->flush();
            $this->addFlash('success', 'Le volontaire a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_admin_volontaire');
    }
}
