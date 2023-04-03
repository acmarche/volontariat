<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/image')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ImageController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper)
    {
    }

    #[Route(path: '/new/{id}', name: 'volontariat_admin_image_edit', methods: ['GET'])]
    public function editAction(Association $association): Response
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_image_upload', ['id' => $association->getId()]))
            ->getForm();

        $images = $this->fileHelper->getImages($association);
        $deleteForm = $this->createDeleteForm($association->getId());

        return $this->render('@Volontariat/admin/image/edit.html.twig', [
            'images' => $images,
            'form_delete' => $deleteForm->createView(),
            'association' => $association,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/upload/{id}', name: 'bottin_admin_image_upload')]
    public function upload(Request $request, Fiche $fiche): Response
    {
        $ficheImage = new FicheImage($fiche);
        /**
         * @var UploadedFile $file
         */
        $file = $request->files->get('file');
        $nom = str_replace('.'.$file->getClientOriginalExtension(), '', $file->getClientOriginalName());
        $ficheImage->setMime($file->getMimeType());
        $ficheImage->setImageName($file->getClientOriginalName());
        $ficheImage->setImage($file);
        try {
            $this->uploadHandler->upload($ficheImage, 'image');
        } catch (Exception $exception) {
            return $this->render(
                '@AcMarcheBottin/admin/upload/_response_fail.html.twig',
                ['error' => $exception->getMessage()]
            );
        }
        $this->imageRepository->persist($ficheImage);
        $this->imageRepository->flush();

        return $this->render('@AcMarcheBottin/admin/upload/_response_ok.html.twig');
    }

    #[Route(path: '/upload/{id}', name: 'volontariat_admin_image_upload', methods: ['POST'])]
    public function uploadAction(Request $request, Association $association): Response
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');

            if ($file instanceof UploadedFile) {
                $fileName = md5(uniqid()).'.'.$file->guessClientExtension();

                try {
                    $this->fileHelper->uploadFile($association, $file, $fileName);
                } catch (FileException $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }

            return new Response('okid');
        }

        return new Response('ko');
    }

    #[Route(path: '/delete/{id}', name: 'volontariat_admin_image_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, Association $association): RedirectResponse
    {
        $form = $this->createDeleteForm($association->getId());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $files = $request->get('img', false);

            if (!$files) {
                $this->addFlash('error', "Vous n'avez sélectionnez aucune photo");

                return $this->redirectToRoute('volontariat_admin_image_edit', ['id' => $association->getId()]);
            }

            foreach ($files as $filename) {
                try {
                    $this->fileHelper->deleteOneDoc($association, $filename);
                    $this->addFlash('success', "L'image $filename a bien été supprimée");
                } catch (FileException) {
                    $this->addFlash('error', "L'image  $filename n'a pas pu être supprimée. ");
                }
            }
        }

        return $this->redirectToRoute('volontariat_admin_image_edit', ['id' => $association->getId()]);
    }

    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_image_delete', ['id' => $id]))
            ->setMethod(Request::METHOD_DELETE)
            ->add(
                'submit',
                SubmitType::class,
                ['label' => 'Supprimer les images sélectionnées', 'attr' => ['class' => 'btn-danger btn-xs']]
            )
            ->getForm();
    }
}
