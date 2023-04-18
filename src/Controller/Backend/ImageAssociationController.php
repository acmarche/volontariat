<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Association;
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

#[Route(path: '/backend/association/images')]
#[IsGranted('ROLE_VOLONTARIAT')]
class ImageAssociationController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper)
    {
    }

    #[Route(path: '/{id}', name: 'volontariat_backend_images_association', methods: ['GET'])]
    #[IsGranted('edit', subject: 'association')]
    public function edit(Request $request, Association $association): Response
    {
        $form = $this->createForm(ImageDropZoneType::class);

        $images = $this->fileHelper->getImages($association);

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
                        $this->fileHelper->uploadFile($association, $file, $fileName);
                    } catch (FileException $error) {
                        $this->addFlash('error', $error->getMessage());
                    }
                }
            }

            return $this->redirectToRoute('volontariat_dashboard');
        }

        return $this->render(
            '@Volontariat/backend/association/images_edit.html.twig',
            [
                'images' => $images,
                'association' => $association,
                'form' => $form->createView(),
            ]
        );
    }

#[Route(path: '/delete/{id}', name: 'volontariat_backend_image_association_delete', methods: ['POST'])]
    public function delete(Request $request, Association $association): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$association->getId(), $request->request->get('_token'))) {

            $all = $request->request->all();
            $files = $all['img'];
            foreach ($files as $filename) {
                try {
                    $this->fileHelper->deleteOneDoc($association, $filename);
                    $this->addFlash('success', "L'image $filename a bien été supprimée");
                } catch (FileException) {
                    $this->addFlash('error', "L'image  $filename n'a pas pu être supprimée. ");
                }
            }
        }

        return $this->redirectToRoute('volontariat_admin_association_show', ['id' => $association->getId()]);
    }
}
