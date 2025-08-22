<?php

namespace AcMarche\Volontariat\Controller\Backend;

use Exception;
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

#[IsGranted('ROLE_VOLONTARIAT')]
class ImageAssociationController extends AbstractController
{
    use getAssociationTrait;

    public function __construct(private FileHelper $fileHelper) {}

    #[Route(path: '/backend/association/images/', name: 'volontariat_backend_images_association', methods: ['GET'])]
    public function edit(Request $request): Response
    {
        if (($hasAssociation = $this->hasAssociation()) instanceof Response) {
            return $hasAssociation;
        }

        $this->denyAccessUnlessGranted('edit', $this->association);

        $form = $this->createForm(ImageDropZoneType::class);

        $images = $this->fileHelper->getImages($this->association);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('volontariat_dashboard');
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@Volontariat/backend/association/images_edit.html.twig',
            [
                'images' => $images,
                'association' => $this->association,
                'form' => $form,
            ],
            $response,
        );
    }

    #[Route(path: '/backend/association/images/upload/new/', name: 'volontariat_backend_association_upload_file', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        if ($this->hasAssociation() instanceof Response) {
            return new JsonResponse(['empty']);
        }

        $file = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            try {
                $this->fileHelper->treatmentFile($this->association, $file);
            } catch (Exception $e) {
                return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        }

        return new JsonResponse(['empty']);
    }

    #[Route(path: '/backend/association/images/delete', name: 'volontariat_backend_image_association_delete', methods: ['POST'])]
    public function delete(Request $request): RedirectResponse
    {
        if ($this->hasAssociation() instanceof Response) {
            return $this->redirectToRoute('volontariat_backend_images_association');
        }

        $this->denyAccessUnlessGranted('edit', $this->association);
        if ($this->isCsrfTokenValid('delete'.$this->association->getId(), $request->request->get('_token'))) {
            $all = $request->request->all();
            $files = $all['img'];
            foreach ($files as $file) {
                try {
                    $this->fileHelper->deleteOneDoc($this->association, $file);
                    $this->addFlash('success', sprintf("L'image %s a bien été supprimée", $file));
                } catch (FileException) {
                    $this->addFlash('error', sprintf("L'image  %s n'a pas pu être supprimée. ", $file));
                }
            }
        }

        return $this->redirectToRoute('volontariat_backend_images_association');
    }
}
