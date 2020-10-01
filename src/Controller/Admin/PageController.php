<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\Form\Admin\PageType;
use AcMarche\Volontariat\Service\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Page controller.
 *
 * @Route("/admin/page")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class PageController extends AbstractController
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
     * Lists all Page entities.
     *
     * @Route("/", name="volontariat_admin_page", methods={"GET"})
     *
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pages = $em->getRepository(Page::class)->findAll();

        return $this->render(
            '@Volontariat/admin/page/index.html.twig',
            array(
                'pages' => $pages,
            )
        );
    }

    /**
     * Displays a form to create a new Page page.
     *
     * @Route("/new", name="volontariat_admin_page_new", methods={"GET","POST"})
     *
     */
    public function newAction(Request $request)
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();
            $this->addFlash("success", "La page a bien été ajoutée");

            return $this->redirectToRoute('volontariat_admin_page_show', ['id' => $page->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/page/new.html.twig',
            array(
                'page' => $page,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Page page.
     *
     * @Route("/{id}", name="volontariat_admin_page_show", methods={"GET"})
     *
     */
    public function showAction(Page $page)
    {
        $deleteForm = $this->createDeleteForm($page);
        $images = $this->fileHelper->getImages($page);

        return $this->render(
            '@Volontariat/admin/page/show.html.twig',
            array(
                'page' => $page,
                'images' => $images,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Page page.
     *
     * @Route("/{id}/edit", name="volontariat_admin_page_edit", methods={"GET","POST"})
     *
     */
    public function editAction(Request $request, Page $page)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(PageType::class, $page)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash("success", "La page a bien été modifiée");

            return $this->redirectToRoute('volontariat_admin_page_show', ['id' => $page->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/page/edit.html.twig',
            array(
                'page' => $page,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Page page.
     *
     * @Route("/{id}", name="volontariat_admin_page_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Page $page)
    {
        $form = $this->createDeleteForm($page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($page);
            $em->flush();
            $this->addFlash("success", "La page a bien été supprimée");
        }

        return $this->redirectToRoute('volontariat_admin_page');
    }

    /**
     * Creates a form to delete a Page page by id.
     *
     * @param mixed $id The page id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Page $page)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_page_delete', array('id' => $page->getId())))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
