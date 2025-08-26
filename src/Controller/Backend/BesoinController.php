<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Form\Admin\BesoinType;
use AcMarche\Volontariat\Message\BesoinCreated;
use AcMarche\Volontariat\Repository\BesoinRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use AcMarche\Volontariat\Security\RolesEnum;
#[IsGranted(RolesEnum::volontaire->value)]
class BesoinController extends AbstractController
{
    use getAssociationTrait;

    public function __construct(
        private readonly BesoinRepository $besoinRepository,
        private readonly MessageBusInterface $messageBus,
    ) {}

    #[Route(path: '/backend/besoin/index', name: 'volontariat_backend_besoin', methods: ['GET'])]
    public function index(): Response
    {
        if (($hasAssociation = $this->hasAssociation()) instanceof Response) {
            return $hasAssociation;
        }

        $annonces = $this->besoinRepository->findByAssociation($this->association);

        return $this->render(
            '@Volontariat/backend/besoin/index.html.twig',
            [
                'association' => $this->association,
                'annonces' => $annonces,
            ],
        );
    }

    #[Route(path: '/backend/besoin/new', name: 'volontariat_backend_besoin_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if (($hasAssociation = $this->hasAssociation()) instanceof Response) {
            return $hasAssociation;
        }

        $besoin = new Besoin();
        $besoin->setAssociation($this->association);
        if (!$this->association->valider) {
            $this->addFlash('danger', 'Votre association doit être validée avant.');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        $form = $this->createForm(BesoinType::class, $besoin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $besoin->setUuid($besoin->generateUuid());
            $this->besoinRepository->persist($besoin);
            $this->besoinRepository->flush();
            $this->addFlash('success', 'Le besoin a bien été ajouté');
            $this->messageBus->dispatch(new BesoinCreated($besoin->getId()));

            return $this->redirectToRoute('volontariat_backend_besoin');
        }

        return $this->render(
            '@Volontariat/backend/besoin/new.html.twig',
            [
                'besoin' => $besoin,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/backend/besoin/{uuid}/edit', name: 'volontariat_backend_besoin_edit')]
    #[IsGranted('edit', subject: 'besoin')]
    public function edit(Request $request,#[MapEntity(expr: 'repository.findOneByUuid(uuid)')]  Besoin $besoin): Response
    {
        $form = $this->createForm(BesoinType::class, $besoin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->besoinRepository->flush();

            $this->addFlash('success', 'Le besoin a bien été modifié');

            return $this->redirectToRoute('volontariat_backend_besoin');
        }

        return $this->render(
            '@Volontariat/backend/besoin/new.html.twig',
            [
                'besoin' => $besoin,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/backend/besoin/delete', name: 'volontariat_backend_besoin_delete', methods: ['POST'])]
    public function delete(Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('deletedd', $request->request->get('_token'))) {
            $uid = $request->request->get('id');
            $besoin = $this->besoinRepository->findOneBy(['uuid' => $uid]);
            if ($besoin && $this->isGranted('edit', $besoin)) {
                $this->besoinRepository->remove($besoin);
                $this->besoinRepository->flush();
            }

            $this->addFlash('success', 'Le besoin a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_dashboard');
    }
}
