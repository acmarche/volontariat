<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Page;
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
 * @Route("/admin/page/images")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class ImagePageController extends AbstractController
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
     * @Route("/new/{id}", name="volontariat_admin_page_image_edit", methods={"GET"})
     *
     *
     */
    public function editAction(Page $page)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_page_image_upload', array('id' => $page->getId())))
            ->getForm();

        $images = $this->fileHelper->getImages($page);
        $deleteForm = $this->createDeleteForm($page->getId());

        return $this->render(
            'admin/imagePage/edit.html.twig',
            [
                'images' => $images,
                'form_delete' => $deleteForm->createView(),
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/upload/{id}", name="volontariat_admin_page_image_upload", methods={"POST"})
     *
     */
    public function uploadAction(Request $request, Page $page)
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');

            if ($file instanceof UploadedFile) {
                $fileName = md5(uniqid()).'.'.$file->guessClientExtension();

                try {
                    $this->fileHelper->uploadFile($page, $file, $fileName);
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
     * @Route("/delete/{id}", name="volontariat_admin_page_image_delete", methods={"DELETE"})
     *
     */
    public function deleteAction(Request $request, Page $page)
    {
        $form = $this->createDeleteForm($page->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $request->get('img', false);

            if (!$files) {
                $this->addFlash('error', "Vous n'avez sélectionnez aucune photo");

                return $this->redirectToRoute('volontariat_admin_image_edit', array('id' => $page->getId()));
            }

            foreach ($files as $filename) {
                try {
                    $this->fileHelper->deleteOneDoc($page, $filename);
                    $this->addFlash('success', "L'image $filename a bien été supprimée");
                } catch (FileException $e) {
                    $this->addFlash('error', "L'image  $filename n'a pas pu être supprimée. ");
                }
            }
        }

        return $this->redirectToRoute('volontariat_admin_page_image_edit', array('id' => $page->getId()));
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
            ->setAction($this->generateUrl('volontariat_admin_page_image_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                array('label' => 'Supprimer les images sélectionnées', 'attr' => array('class' => 'btn-danger btn-xs'))
            )
            ->getForm();
    }
}
