<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Activite;
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
 *
 * @Route("/backend/activite/images")
 * @IsGranted("ROLE_VOLONTARIAT")
 */
class ImageActiviteController extends AbstractController
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
     * @Route("/new/{id}", name="volontariat_backend_image_activite", methods={"GET"})
     * @IsGranted("edit", subject="activite")
     *
     */
    public function editAction(Activite $activite)
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('volontariat_backend_image_activite_upload', array('id' => $activite->getId()))
            )
            ->setMethod('POST')
            ->getForm();

        $images = $this->fileHelper->getImages($activite);
        $deleteForm = $this->createDeleteForm($activite->getId());
        $association = $activite->getAssociation();

        return $this->render(
            'backend/image_activite/edit.html.twig',
            array(
                'images' => $images,
                'form_delete' => $deleteForm->createView(),
                'activite' => $activite,
                'association'=>$association,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Route("/upload/{id}", name="volontariat_backend_image_activite_upload", methods={"POST"})
     * @IsGranted("edit", subject="activite")
     */
    public function uploadAction(Request $request, Activite $activite)
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
     * @Route("/delete/{id}", name="volontariat_backend_image_activite_delete", methods={"DELETE"})
     * @IsGranted("edit", subject="activite")
     */
    public function deleteAction(Request $request, Activite $activite)
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
                } catch (FileException $e) {
                    $this->addFlash('error', "L'image  $filename n'a pas pu être supprimée. ");
                }
            }
        }

        return $this->redirectToRoute('volontariat_backend_image_activite', array('id' => $activite->getId()));
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
            ->setAction($this->generateUrl('volontariat_backend_image_activite_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                array('label' => 'Supprimer les images sélectionnées', 'attr' => array('class' => 'btn-danger'))
            )
            ->getForm();
    }
}