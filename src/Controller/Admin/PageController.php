<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\Form\Admin\PageType;
use AcMarche\Volontariat\Repository\PageRepository;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/page')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class PageController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper, private PageRepository $pageRepository)
    {
    }

    #[Route(path: '/', name: 'volontariat_admin_page', methods: ['GET'])]
    public function indexAction(): Response
    {
        $pages = $this->pageRepository->findAll();

        return $this->render(
            '@Volontariat/admin/page/index.html.twig',
            [
                'pages' => $pages,
            ]
        );
    }

    #[Route(path: '/new', name: 'volontariat_admin_page_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->pageRepository->persist($page);
            $this->pageRepository->flush();
            $this->addFlash('success', 'La page a bien été ajoutée');

            return $this->redirectToRoute('volontariat_admin_page_show', ['id' => $page->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/page/new.html.twig',
            [
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'volontariat_admin_page_show', methods: ['GET'])]
    public function showAction(Page $page): Response
    {
        $images = $this->fileHelper->getImages($page);
        $docs = $this->fileHelper->getDocuments($page);

        return $this->render(
            '@Volontariat/admin/page/show.html.twig',
            [
                'page' => $page,
                'images' => $images,
                'documents' => $docs,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_admin_page_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Page $page): Response
    {
        $form = $this->createForm(PageType::class, $page);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->pageRepository->flush();
            $this->addFlash('success', 'La page a bien été modifiée');

            return $this->redirectToRoute('volontariat_admin_page_show', ['id' => $page->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/page/edit.html.twig',
            [
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'volontariat_admin_page_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $this->pageRepository->remove($page);
            $this->pageRepository->flush();
            $this->addFlash('success', 'La page a bien été supprimée');
        }

        return $this->redirectToRoute('volontariat_admin_page');
    }
}
