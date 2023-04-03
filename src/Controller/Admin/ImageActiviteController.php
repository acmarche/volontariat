<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/activite/images')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ImageActiviteController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper)
    {
    }

    #[Route(path: '/new/{id}', name: 'volontariat_admin_activite_image_edit', methods: ['GET'])]
    public function editaction(Activite $activite): Response
    {
        $form = $this->createFormBuilder()
            ->setaction(
                $this->generateUrl('volontariat_admin_activite_image_upload', array('id' => $activite->getId()))
            )
            ->getForm();
        $images = $this->fileHelper->getImages($activite);
        $deleteForm = $this->createDeleteForm($activite->getId());

        return $this->render(
            '@Volontariat/admin/imageActivite/edit.html.twig',
            [
                'images' => $images,
                'form_delete' => $deleteForm->createView(),
                'activite' => $activite,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/upload/{id}', name: 'volontariat_admin_activite_image_upload', methods: ['POST'])]
    public function uploadaction(Request $request, Activite $activite): Response
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');

            if ($file instanceof UploadedFile) {
                $fileName = md5(uniqid()).'.'.$file->guessClientExtension();

                try {
                    $this->fileHelper->uploadFile($activite, $file, $fileName);
                } catch (FileException $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }

            return new Response('okid');
        }

        return new Response('ko');
    }

    #[Route(path: '/delete/{id}', name: 'volontariat_admin_activite_image_delete', methods: ['DELETE'])]
    public function deleteaction(Request $request, Activite $activite): RedirectResponse
    {
        $form = $this->createDeleteForm($activite->getId());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $files = $request->get('img', false);

            if (!$files) {
                $this->addFlash('error', "Vous n'avez sélectionnez aucune photo");

                return $this->redirectToRoute('volontariat_admin_image_edit', array('id' => $activite->getId()));
            }

            foreach ($files as $filename) {
                try {
                    $this->fileHelper->deleteOneDoc($activite, $filename);
                    $this->addFlash('success', "L'image $filename a bien été supprimée");
                } catch (FileException) {
                    $this->addFlash('error', "L'image  $filename n'a pas pu être supprimée. ");
                }
            }
        }

        return $this->redirectToRoute('volontariat_admin_activite_image_edit', array('id' => $activite->getId()));
    }

    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setaction($this->generateUrl('volontariat_admin_activite_image_delete', array('id' => $id)))
            ->setMethod(Request::METHOD_DELETE)
            ->add(
                'submit',
                SubmitType::class,
                array('label' => 'Supprimer les images sélectionnées', 'attr' => array('class' => 'btn-danger btn-xs'))
            )
            ->getForm();
    }
}
