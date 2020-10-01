<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
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
 *
 * @Route("/admin/image")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class ImageController extends AbstractController
{
    /**
     * @var FileHelper
     */
    private $fileHelper;

    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    /**
     * Displays a form to create a new Image entity.
     *
     * @Route("/new/{id}", name="volontariat_admin_image_edit", methods={"GET"})
     *
     */
    public function editAction(Association $association)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_image_upload', array('id' => $association->getId())))
            ->setMethod('POST')
            ->getForm();

        $images = $this->fileHelper->getImages($association);
        $deleteForm = $this->createDeleteForm($association->getId());

        return $this->render('@Volontariat/admin/image/edit.html.twig', array(
            'images' => $images,
            'form_delete' => $deleteForm->createView(),
            'association' => $association,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/upload/{id}", name="volontariat_admin_image_upload", methods={"POST"})
     *
     */
    public function uploadAction(Request $request, Association $association)
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
     *
     * @Route("/delete/{id}", name="volontariat_admin_image_delete", methods={"DELETE"})
     *
     */
    public function deleteAction(Request $request, Association $association)
    {
        $form = $this->createDeleteForm($association->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $request->get('img', false);

            if (!$files) {
                $this->addFlash('error', "Vous n'avez sélectionnez aucune photo");

                return $this->redirectToRoute('volontariat_admin_image_edit', array('id' => $association->getId()));
            }

            foreach ($files as $filename) {
                try {
                    $this->fileHelper->deleteOneDoc($association, $filename);
                    $this->addFlash('success', "L'image $filename a bien été supprimée");
                } catch (FileException $e) {
                    $this->addFlash('error', "L'image  $filename n'a pas pu être supprimée. ");
                }
            }
        }

        return $this->redirectToRoute('volontariat_admin_image_edit', array('id' => $association->getId()));
    }

    /**
     * Creates a form to delete a Image entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_image_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                array('label' => 'Supprimer les images sélectionnées', 'attr' => array('class' => 'btn-danger btn-xs'))
            )
            ->getForm();
    }
}
