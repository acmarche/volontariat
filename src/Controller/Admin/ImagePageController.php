<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Exception;
use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\Form\Admin\ImageDropZoneType;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ImagePageController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper) {}

    #[Route(path: '/admin/page/images/new/{id}', name: 'volontariat_admin_page_image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page): Response
    {
        $form = $this->createForm(ImageDropZoneType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', "L'image a bien été ajoutée");

            return $this->redirectToRoute('volontariat_admin_page_show', ['id' => $page->getId()]);
        }

        $images = $this->fileHelper->getFiles($page);

        return $this->render(
            '@Volontariat/admin/imagePage/edit.html.twig',
            [
                'images' => $images,
                'page' => $page,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/admin/page/images/upload/new/{id}', name: 'volontariat_admin_page_upload_file', methods: ['POST'])]
    public function upload(Request $request, Page $page): JsonResponse
    {
        $file = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            try {
                $this->fileHelper->treatmentFile($page, $file);
            } catch (Exception $e) {
                return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        }

        return new JsonResponse(['empty']);
    }

    #[Route(path: '/admin/page/images/delete/{id}', name: 'volontariat_admin_page_image_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $all = $request->request->all();
            $files = $all['img'];
            foreach ($files as $file) {
                try {
                    $this->fileHelper->deleteOneDoc($page, $file);
                    $this->addFlash('success', sprintf("L'image %s a bien été supprimée", $file));
                } catch (FileException) {
                    $this->addFlash('error', sprintf("L'image  %s n'a pas pu être supprimée. ", $file));
                }
            }
        }

        return $this->redirectToRoute('volontariat_admin_page_show', ['id' => $page->getId()]);
    }
}
