<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\Form\Admin\ImageDropZoneType;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use AcMarche\Volontariat\Security\RolesEnum;
#[IsGranted(RolesEnum::association->value)]
class ImagePageController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper)
    {
    }

    #[Route(path: '/admin/page/images/new/{id}', name: 'volontariat_admin_page_image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page): Response
    {
        $form = $this->createForm(ImageDropZoneType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var UploadedFile[] $data
             */
            $data = $form->getData();
            foreach ($data['file'] as $file) {
                if ($file instanceof UploadedFile) {
                    try {
                        $this->fileHelper->treatmentFile($page, $file);
                    } catch (\Exception $exception) {
                        $this->addFlash('danger', 'Erreur upload image: '.$exception->getMessage());
                    }
                }
            }

            $this->addFlash('success', "Les images ont été traitées");

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
