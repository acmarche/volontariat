<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Form\Admin\BesoinType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

#[Route(path: '/admin/besoin')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class BesoinController extends AbstractController
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    #[Route(path: '/', name: 'volontariat_admin_besoin')]
    public function indexaction() : Response
    {
        $em = $this->managerRegistry->getManager();
        $entities = $em->getRepository(Besoin::class)->findAll();
        return $this->render('@Volontariat/admin/besoin/index.html.twig', array(

            'entities' => $entities,
        ));
    }

    #[Route(path: '/new/{id}', name: 'volontariat_admin_besoin_new', methods: ['GET', 'POST'])]
    public function newaction(Request $request, Association $association) : Response
    {
        $besoin = new Besoin();
        $besoin->setAssociation($association);
        $form = $this->createForm(BesoinType::class, $besoin)
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($besoin);
            $em->flush();
            $this->addFlash("success", "Le besoin a bien été ajouté");

            return $this->redirectToRoute('volontariat_admin_besoin_show', ['id' => $besoin->getId()]);
        }
        return $this->render('@Volontariat/admin/besoin/new.html.twig', array(
            'besoin' => $besoin,
            'form' => $form->createView(),
        ));
    }

    #[Route(path: '/{id}/show', name: 'volontariat_admin_besoin_show')]
    public function showaction(Besoin $besoin) : Response
    {
        $deleteForm = $this->createDeleteForm($besoin);
        return $this->render('@Volontariat/admin/besoin/show.html.twig', array(
            'besoin' => $besoin,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_admin_besoin_edit')]
    public function editaction(Request $request, Besoin $besoin) : Response
    {
        $em = $this->managerRegistry->getManager();
        $form = $this->createForm(BesoinType::class, $besoin)
            ->add('submit', SubmitType::class, array('label' => 'Update'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le besoin a bien été modifié');

            return $this->redirectToRoute('volontariat_admin_besoin_show', ['id' => $besoin->getId()]);
        }
        return $this->render('@Volontariat/admin/besoin/edit.html.twig', array(
            'besoin' => $besoin,
            'form' => $form->createView(),
        ));
    }

    #[Route(path: '/{id}/delete', name: 'volontariat_admin_besoin_delete', methods: ['DELETE'])]
    public function deleteaction(Besoin $besoin, Request $request) : RedirectResponse
    {
        $form = $this->createDeleteForm($besoin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $em->remove($besoin);
            $em->flush();

            $this->addFlash('success', 'Le besoin a bien été supprimé');
        }
        return $this->redirectToRoute('volontariat_admin_besoin');
    }
    private function createDeleteForm(Besoin $besoin): FormInterface
    {
        return $this->createFormBuilder()
            ->setaction($this->generateUrl('volontariat_admin_besoin_delete', array('id' => $besoin->getId())))
            ->setMethod(Request::METHOD_DELETE)
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
