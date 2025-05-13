<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
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

#[Route(path: '/admin/images/association')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ImageAssociationController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper) {}

    #[Route(path: '/new/{id}', name: 'volontariat_admin_image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Association $association): Response
    {
        $form = $this->createForm(ImageDropZoneType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('volontariat_admin_association_show', ['id' => $association->getId()]);
        }

        $images = $this->fileHelper->getImages($association);

        return $this->render('@Volontariat/admin/imageAssociation/edit.html.twig', [
            'images' => $images,
            'association' => $association,
            'form' => $form,
        ]);
    }

    #[Route(path: '/upload/new/{id}', name: 'volontariat_admin_association_upload_file', methods: ['POST'])]
    public function upload(Request $request, Association $association): JsonResponse
    {
        $file = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            try {
                $this->fileHelper->treatmentFile($association, $file);
            } catch (\Exception $e) {
                return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        }

        return new JsonResponse(['empty']);
    }

    #[Route(path: '/delete/{id}', name: 'volontariat_admin_image_association_delete', methods: ['POST'])]
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
