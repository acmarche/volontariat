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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/page/images')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ImagePageController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper)
    {
    }

    #[Route(path: '/new/{id}', name: 'volontariat_admin_page_image_edit', methods: ['GET', 'POST'])]
    public function editaction(Request $request, Page $page): Response
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
                    $orignalName = preg_replace(
                        '#.'.$file->guessClientExtension().'#',
                        '',
                        $file->getClientOriginalName()
                    );
                    $fileName = $orignalName.'-'.uniqid().'.'.$file->guessClientExtension();

                    try {
                        $this->fileHelper->uploadFile($page, $file, $fileName);
                    } catch (FileException $error) {
                        $this->addFlash('error', $error->getMessage());
                    }
                }
            }
            $this->addFlash('success', "L'image a bien été ajoutée");

            return $this->redirectToRoute('volontariat_admin_page_show', ['id' => $page->getId()]);
        }

        $images = $this->fileHelper->getFiles($page);

        return $this->render(
            '@Volontariat/admin/imagePage/edit.html.twig',
            [
                'images' => $images,
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/delete/{id}', name: 'volontariat_admin_page_image_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {

            $all = $request->request->all();
            $files = $all['img'];
            foreach ($files as $filename) {
                try {
                    $this->fileHelper->deleteOneDoc($page, $filename);
                    $this->addFlash('success', "L'image $filename a bien été supprimée");
                } catch (FileException) {
                    $this->addFlash('error', "L'image  $filename n'a pas pu être supprimée. ");
                }
            }
        }

        return $this->redirectToRoute('volontariat_admin_page_show', ['id' => $page->getId()]);
    }
}
