<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormInterface;
use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Service\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Image controller.
 */
#[Route(path: '/admin/activite/images')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ImageActiviteController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper)
    {
    }
    /**
     * Displays a form to create a new Image entity.
     *
     *
     */
    #[Route(path: '/new/{id}', name: 'volontariat_admin_activite_image_edit', methods: ['GET'])]
    public function editAction(Activite $activite) : Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
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
    public function uploadAction(Request $request, Activite $activite) : Response
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
    }
    /**
     * Deletes a Image entity.
     *
     *
     */
    #[Route(path: '/delete/{id}', name: 'volontariat_admin_activite_image_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, Activite $activite) : RedirectResponse
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
            ->setAction($this->generateUrl('volontariat_admin_activite_image_delete', array('id' => $id)))
            ->setMethod(Request::METHOD_DELETE)
            ->add(
                'submit',
                SubmitType::class,
                array('label' => 'Supprimer les images sélectionnées', 'attr' => array('class' => 'btn-danger btn-xs'))
            )
            ->getForm();
    }
}
