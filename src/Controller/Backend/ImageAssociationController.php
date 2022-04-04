<?php

namespace AcMarche\Volontariat\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormInterface;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Service\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Image controller.
 */
#[Route(path: '/backend/association/images')]
#[IsGranted('ROLE_VOLONTARIAT')]
class ImageAssociationController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper)
    {
    }
    /**
     * Displays a form to create a new Image entity.
     */
    #[Route(path: '/{id}', name: 'volontariat_backend_images_association', methods: ['GET'])]
    #[IsGranted('edit', subject: 'association')]
    public function editAction(Association $association) : Response
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_backend_image_upload', array('id' => $association->getId())))
            ->setMethod(Request::METHOD_POST)
            ->getForm();
        $images = $this->fileHelper->getImages($association);
        $deleteForm = $this->createDeleteForm($association->getId());
        return $this->render(
            '@Volontariat/backend/image_association/edit.html.twig',
            [
                'images' => $images,
                'form_delete' => $deleteForm->createView(),
                'association' => $association,
                'form' => $form->createView(),
            ]
        );
    }
    #[Route(path: '/upload/{id}', name: 'volontariat_backend_image_upload', methods: ['POST'])]
    #[IsGranted('edit', subject: 'association')]
    public function uploadAction(Request $request, Association $association) : Response
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
    }
    /**
     * Deletes a Image entity.
     */
    #[Route(path: '/delete/{id}', name: 'volontariat_backend_image_delete', methods: ['DELETE'])]
    #[IsGranted('edit', subject: 'association')]
    public function deleteAction(Request $request, Association $association) : RedirectResponse
    {
        $form = $this->createDeleteForm($association->getId());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $files = $request->get('img', false);

            if (!$files) {
                $this->addFlash('error', "Vous n'avez sélectionnez aucune photo");

                return $this->redirectToRoute(
                    'volontariat_backend_images_association',
                    array('id' => $association->getId())
                );
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
        return $this->redirectToRoute('volontariat_backend_images_association', array('id' => $association->getId()));
    }
    /**
     * Creates a form to delete a Image entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_backend_image_delete', array('id' => $id)))
            ->setMethod(Request::METHOD_DELETE)
            ->add(
                'submit',
                SubmitType::class,
                array('label' => 'Supprimer les images sélectionnées', 'attr' => array('class' => 'btn-danger'))
            )
            ->getForm();
    }
}
