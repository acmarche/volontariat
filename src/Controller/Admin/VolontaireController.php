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

use AcMarche\Volontariat\Security\RolesEnum;
#[IsGranted(RolesEnum::admin->value)]
class VolontaireController extends AbstractController
{
    public function __construct(
        private VolontaireRepository $volontaireRepository,
    ) {}

    #[Route(path: '/admin/volontaire/', name: 'volontariat_admin_volontaire')]
    public function index(Request $request): Response
    {
        $data = [];

        $form = $this->createForm(SearchVolontaireType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
        }

        $volontaires = $this->volontaireRepository->search($data);

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@Volontariat/admin/volontaire/index.html.twig',
            [
                'form' => $form,
                'volontaires' => $volontaires,
            ]
            , $response,
        );
    }

    #[Route(path: '/admin/volontaire/new', name: 'volontariat_admin_volontaire_new', methods: ['GET', 'POST'])]
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

    #[Route(path: '/admin/volontaire/{id}/show', name: 'volontariat_admin_volontaire_show')]
    public function show(Volontaire $volontaire): Response
    {
        return $this->render(
            '@Volontariat/admin/volontaire/show.html.twig',
            [
                'volontaire' => $volontaire,
            ],
        );
    }

    #[Route(path: '/admin/volontaire/{id}/edit', name: 'volontariat_admin_volontaire_edit')]
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

    #[Route(path: '/admin/volontaire/{id}/delete', name: 'volontariat_admin_volontaire_delete', methods: ['POST'])]
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
