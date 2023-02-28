<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormInterface;
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

#[Route(path: '/admin/page/images')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class ImagePageController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper)
    {
    }

    #[Route(path: '/new/{id}', name: 'volontariat_admin_page_image_edit', methods: ['GET'])]
    public function editAction(Page $page) : Response
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_page_image_upload', array('id' => $page->getId())))
            ->getForm();
        $images = $this->fileHelper->getFiles($page);
        $deleteForm = $this->createDeleteForm($page->getId());
        return $this->render(
            '@Volontariat/admin/imagePage/edit.html.twig',
            [
                'images' => $images,
                'form_delete' => $deleteForm->createView(),
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }
    #[Route(path: '/upload/{id}', name: 'volontariat_admin_page_image_upload', methods: ['POST'])]
    public function uploadAction(Request $request, Page $page) : Response
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');

            if ($file instanceof UploadedFile) {
                $orignalName = preg_replace('#.'.$file->guessClientExtension().'#','',$file->getClientOriginalName());
                $fileName = $orignalName.'-'.uniqid().'.'.$file->guessClientExtension();

                try {
                    $this->fileHelper->uploadFile($page, $file, $fileName);
                } catch (FileException $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }

            return new Response('okid');
        }
        return new Response('ko');
    }

    #[Route(path: '/delete/{id}', name: 'volontariat_admin_page_image_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, Page $page) : RedirectResponse
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
                } catch (FileException) {
                    $this->addFlash('error', "L'image  $filename n'a pas pu être supprimée. ");
                }
            }
        }
        return $this->redirectToRoute('volontariat_admin_page_image_edit', array('id' => $page->getId()));
    }

    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_page_image_delete', array('id' => $id)))
            ->setMethod(Request::METHOD_DELETE)
            ->add(
                'submit',
                SubmitType::class,
                array('label' => 'Supprimer les images sélectionnées', 'attr' => array('class' => 'btn-danger btn-xs'))
            )
            ->getForm();
    }
}
