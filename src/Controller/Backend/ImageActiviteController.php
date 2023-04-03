<?php

namespace AcMarche\Volontariat\Controller\Backend;

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

#[Route(path: '/backend/activite/images')]
#[IsGranted('ROLE_VOLONTARIAT')]
class ImageActiviteController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper)
    {
    }

    #[Route(path: '/new/{id}', name: 'volontariat_backend_image_activite', methods: ['GET'])]
    #[IsGranted('edit', subject: 'activite')]
    public function edit(Activite $activite): Response
    {
        $form = $this->createFormBuilder()
            ->set(
                $this->generateUrl('volontariat_backend_image_activite_upload', array('id' => $activite->getId()))
            )
            ->setMethod(Request::METHOD_POST)
            ->getForm();
        $images = $this->fileHelper->getImages($activite);
        $deleteForm = $this->createDeleteForm($activite->getId());
        $association = $activite->getAssociation();

        return $this->render(
            '@Volontariat/backend/image_activite/edit.html.twig',
            array(
                'images' => $images,
                'form_delete' => $deleteForm->createView(),
                'activite' => $activite,
                'association' => $association,
                'form' => $form->createView(),
            )
        );
    }

    #[Route(path: '/upload/{id}', name: 'volontariat_backend_image_activite_upload', methods: ['POST'])]
    #[IsGranted('edit', subject: 'activite')]
    public function upload(Request $request, Activite $activite): Response
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

    #[Route(path: '/delete/{id}', name: 'volontariat_backend_image_activite_delete', methods: ['DELETE'])]
    #[IsGranted('edit', subject: 'activite')]
    public function delete(Request $request, Activite $activite): RedirectResponse
    {
        $form = $this->createDeleteForm($activite->getId());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $files = $request->get('img', false);

            if (!$files) {
                $this->addFlash('error', "Vous n'avez sélectionnez aucune photo");

                return $this->redirectToRoute('volontariat_backend_image_activite', array('id' => $activite->getId()));
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

        return $this->redirectToRoute('volontariat_backend_image_activite', array('id' => $activite->getId()));
    }

    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->set($this->generateUrl('volontariat_backend_image_activite_delete', array('id' => $id)))
            ->setMethod(Request::METHOD_DELETE)
            ->add(
                'submit',
                SubmitType::class,
                array('label' => 'Supprimer les images sélectionnées', 'attr' => array('class' => 'btn-danger'))
            )
            ->getForm();
    }
}
