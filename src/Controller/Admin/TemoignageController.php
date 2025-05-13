<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use AcMarche\Volontariat\Entity\Temoignage;
use AcMarche\Volontariat\Form\Admin\TemoignageType;
use AcMarche\Volontariat\Repository\TemoignageRepository;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/temoignage')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class TemoignageController extends AbstractController
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    #[Route(path: '/', name: 'volontariat_admin_temoignage', methods: ['GET'])]
    public function index(TemoignageRepository $temoignageRepository) : Response
    {
        $temoignages = $temoignageRepository->findAll();
        return $this->render('@Volontariat/admin/temoignage/index.html.twig', ['temoignages' => $temoignages]);
    }

    #[Route(path: '/new', name: 'volontariat_admin_temoignage_new', methods: ['GET', 'POST'])]
    public function new(Request $request) : Response
    {
        $temoignage = new Temoignage();
        $form = $this->createForm(TemoignageType::class, $temoignage)
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($temoignage);
            $em->flush();

            $this->addFlash('success', 'temoignage.created_successfully');

            return $this->redirectToRoute('volontariat_admin_temoignage');
        }
        return $this->render(
            '@Volontariat/admin/temoignage/new.html.twig',
            [
                'temoignage' => $temoignage,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], name: 'volontariat_admin_temoignage_show', methods: ['GET'])]
    public function show(Temoignage $temoignage) : Response
    {
        $deleteForm = $this->createDeleteForm($temoignage);
        return $this->render(
            '@Volontariat/admin/temoignage/show.html.twig',
            [
                'temoignage' => $temoignage,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/edit', requirements: ['id' => '\d+'], name: 'volontariat_admin_temoignage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Temoignage $temoignage) : Response
    {
        $form = $this->createForm(TemoignageType::class, $temoignage)
            ->add('submit', SubmitType::class, array('label' => 'Update'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->managerRegistry->getManager()->flush();

            $this->addFlash('success', 'temoignage.updated_successfully');

            return $this->redirectToRoute('volontariat_admin_temoignage_show', ['id' => $temoignage->getId()]);
        }
        return $this->render(
            '@Volontariat/admin/temoignage/edit.html.twig',
            [
                'temoignage' => $temoignage,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'volontariat_admin_temoignage_delete', methods: ['DELETE'])]
    public function delete(Temoignage $temoignage, Request $request) : RedirectResponse
    {
        $form = $this->createDeleteForm($temoignage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $em->remove($temoignage);
            $em->flush();

            $this->addFlash('success', 'Le témoignange a bien été supprimé');
        }
        return $this->redirectToRoute('volontariat_admin_temoignage');
    }
    private function createDeleteForm(Temoignage $temoignage): FormInterface
    {
        return $this->createFormBuilder()
            ->set(
                $this->generateUrl('volontariat_admin_temoignage_delete', array('id' => $temoignage->getId()))
            )
            ->setMethod(Request::METHOD_DELETE)
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
