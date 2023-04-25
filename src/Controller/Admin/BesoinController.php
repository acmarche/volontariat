<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Form\Admin\BesoinType;
use AcMarche\Volontariat\Repository\BesoinRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/besoin')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class BesoinController extends AbstractController
{
    public function __construct(private BesoinRepository $besoinRepository)
    {
    }

    #[Route(path: '/', name: 'volontariat_admin_besoin')]
    public function index(): Response
    {
        $entities = $this->besoinRepository->findAll();

        return $this->render('@Volontariat/admin/besoin/index.html.twig', [
            'entities' => $entities,
        ]);
    }

    #[Route(path: '/new/{id}', name: 'volontariat_admin_besoin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Association $association): Response
    {
        $besoin = new Besoin();
        $besoin->setUuid($besoin->generateUuid());
        $besoin->setAssociation($association);
        $form = $this->createForm(BesoinType::class, $besoin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->besoinRepository->persist($besoin);
            $this->besoinRepository->flush();
            $this->addFlash('success', 'Le besoin a bien été ajouté');

            return $this->redirectToRoute('volontariat_admin_besoin_show', ['id' => $besoin->getId()]);
        }

        return $this->render('@Volontariat/admin/besoin/new.html.twig', [
            'besoin' => $besoin,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/show', name: 'volontariat_admin_besoin_show')]
    public function show(Besoin $besoin): Response
    {
        return $this->render('@Volontariat/admin/besoin/show.html.twig', [
            'besoin' => $besoin,
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_admin_besoin_edit')]
    public function edit(Request $request, Besoin $besoin): Response
    {
        $form = $this->createForm(BesoinType::class, $besoin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->besoinRepository->flush();

            $this->addFlash('success', 'Le besoin a bien été modifié');

            return $this->redirectToRoute('volontariat_admin_besoin_show', ['id' => $besoin->getId()]);
        }

        return $this->render('@Volontariat/admin/besoin/edit.html.twig', [
            'besoin' => $besoin,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'volontariat_admin_besoin_delete', methods: ['POST'])]
    public function delete(Request $request, Besoin $besoin): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$besoin->getId(), $request->request->get('_token'))) {
            $this->besoinRepository->remove($besoin);
            $this->besoinRepository->flush();
            $this->addFlash('success', 'L\'association a bien été supprimée');
        }

        return $this->redirectToRoute('volontariat_admin_association');
    }
}
