<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Temoignage;
use AcMarche\Volontariat\Form\Admin\TemoignageType;
use AcMarche\Volontariat\Repository\TemoignageRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/backend/temoignage')]
#[IsGranted('ROLE_VOLONTARIAT')]
class TemoignageController extends AbstractController
{
    public function __construct(
        private TemoignageRepository $temoignageRepository,
        private ManagerRegistry $managerRegistry
    ) {
    }

    #[Route(path: '/', name: 'volontariat_backend_temoignage', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $temoignages = $this->temoignageRepository->findBy(['user' => $user->getUserIdentifier()]);
        $formDelete = $this->createDeleteForm();

        return $this->render(
            '@Volontariat/backend/temoignage/index.html.twig',
            [
                'temoignages' => $temoignages,
                'form_delete' => $formDelete->createView(),
            ]
        );
    }

    #[Route(path: '/new', name: 'volontariat_backend_temoignage_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        $temoignage = new Temoignage();
        $temoignage->setUser($user->getUserIdentifier());
        $temoignage->setNom($user->getPrenom());
        $form = $this->createForm(TemoignageType::class, $temoignage)
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($temoignage);
            $em->flush();

            $this->addFlash('success', 'temoignage.created_successfully');

            return $this->redirectToRoute('volontariat_backend_temoignage');
        }

        return $this->render(
            '@Volontariat/backend/temoignage/new.html.twig',
            [
                'temoignage' => $temoignage,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/edit', requirements: ['id' => '\d+'], name: 'volontariat_backend_temoignage_edit', methods: [
        'GET',
        'POST',
    ])]
    #[IsGranted('edit', subject: 'temoignage')]
    public function edit(Request $request, Temoignage $temoignage): Response
    {
        $form = $this->createForm(TemoignageType::class, $temoignage)
            ->add('submit', SubmitType::class, array('label' => 'Update'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->managerRegistry->getManager()->flush();

            $this->addFlash('success', 'temoignage.updated_successfully');

            return $this->redirectToRoute('volontariat_backend_temoignage');
        }

        return $this->render(
            '@Volontariat/backend/temoignage/edit.html.twig',
            [
                'temoignage' => $temoignage,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/delete', name: 'volontariat_backend_temoignage_delete', methods: ['DELETE'])]
    public function delete(Request $request): RedirectResponse
    {
        $em = $this->managerRegistry->getManager();
        $idtemoignage = $request->request->get('idtemoignage');
        $temoignage = $em->getRepository(Temoignage::class)->find($idtemoignage);
        if (!$temoignage) {
            $this->addFlash('danger', 'Témoignage introuvable');

            return $this->redirectToRoute('volontariat_dashboard');
        }
        $form = $this->createDeleteForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('delete', $temoignage, "Vous n'avez pas accès a ce témoignage.");

            $em = $this->managerRegistry->getManager();

            $em->remove($temoignage);
            $em->flush();

            $this->addFlash('success', 'Le témoignage a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_dashboard');
    }

    private function createDeleteForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->set($this->generateUrl('volontariat_backend_temoignage_delete'))
            ->setMethod(Request::METHOD_DELETE)
            ->getForm();
    }
}
