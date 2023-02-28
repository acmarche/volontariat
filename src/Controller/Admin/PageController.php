<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\Form\Admin\PageType;
use AcMarche\Volontariat\Service\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/page')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class PageController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper, private ManagerRegistry $managerRegistry)
    {
    }

    #[Route(path: '/', name: 'volontariat_admin_page', methods: ['GET'])]
    public function indexAction() : Response
    {
        $em = $this->managerRegistry->getManager();
        $pages = $em->getRepository(Page::class)->findAll();
        return $this->render(
            '@Volontariat/admin/page/index.html.twig',
            array(
                'pages' => $pages,
            )
        );
    }

    #[Route(path: '/new', name: 'volontariat_admin_page_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request) : Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page)
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
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

    #[Route(path: '/{id}', name: 'volontariat_admin_page_show', methods: ['GET'])]
    public function showAction(Page $page) : Response
    {
        $deleteForm = $this->createDeleteForm($page);
        $images = $this->fileHelper->getImages($page);
        $docs = $this->fileHelper->getDocuments($page);
        return $this->render(
            '@Volontariat/admin/page/show.html.twig',
            array(
                'page' => $page,
                'images' => $images,
                'documents' => $docs,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_admin_page_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Page $page) : Response
    {
        $em = $this->managerRegistry->getManager();
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

    #[Route(path: '/{id}', name: 'volontariat_admin_page_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, Page $page) : RedirectResponse
    {
        $form = $this->createDeleteForm($page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->remove($page);
            $em->flush();
            $this->addFlash("success", "La page a bien été supprimée");
        }
        return $this->redirectToRoute('volontariat_admin_page');
    }

    private function createDeleteForm(Page $page): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_page_delete', array('id' => $page->getId())))
            ->setMethod(Request::METHOD_DELETE)
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
