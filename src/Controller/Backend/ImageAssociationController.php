<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Form\Admin\ImageDropZoneType;
use AcMarche\Volontariat\Service\FileHelper;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ImageAssociationController extends AbstractController
{
    use getAssociationTrait;

    public function __construct(private FileHelper $fileHelper)
    {
    }

    #[Route(path: '/backend/association/images/', name: 'volontariat_backend_images_association', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        if (($hasAssociation = $this->hasAssociation()) instanceof Response) {
            return $hasAssociation;
        }

        $this->denyAccessUnlessGranted('edit', $this->association);

        $form = $this->createForm(ImageDropZoneType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $data */
            $data = $form->getData();
            foreach ($data['file'] as $file) {
                if ($file instanceof UploadedFile) {
                    try {
                        $this->fileHelper->treatmentFile($this->association, $file);
                    } catch (Exception $exception) {
                        $this->addFlash('danger', 'Erreur upload image: '.$exception->getMessage());
                    }
                }
            }

            $this->addFlash('success', 'Les images ont été traitées');

            return $this->redirectToRoute('volontariat_backend_images_association');
        }

        $images = $this->fileHelper->getImages($this->association);

        return $this->render(
            '@Volontariat/backend/association/images_edit.html.twig',
            [
                'images' => $images,
                'association' => $this->association,
                'form' => $form,
            ],
        );
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
                    $this->addFlash('danger', sprintf("L'image  %s n'a pas pu être supprimée. ", $file));
                }
            }
        }

        return $this->redirectToRoute('volontariat_backend_images_association');
    }
}
