<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Admin\LinkAccountType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/link')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class LinkController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private VolontaireRepository $volontaireRepository,
        private AssociationRepository $associationRepository
    ) {
    }

    #[Route(path: '/association/{id}', name: 'volontariat_admin_link_account_association', methods: ['GET', 'POST'])]
    public function association(Request $request, Association $association): Response
    {
        $form = $this->createForm(LinkAccountType::class, ['user' => $association->user]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->treatment($data, $association);

            return $this->redirectToRoute('volontariat_admin_association_show', ['id' => $association->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/link/account.html.twig',
            [
                'object' => $association,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/volontaire/{id}', name: 'volontariat_admin_link_account_volontaire', methods: ['GET', 'POST'])]
    public function volontaire(Request $request, Volontaire $volontaire): Response
    {
        $form = $this->createForm(LinkAccountType::class, ['user' => $volontaire->user]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->treatment($data, $volontaire);

            return $this->redirectToRoute('volontariat_admin_volontaire_show', ['id' => $volontaire->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/link/account.html.twig',
            [
                'object' => $volontaire,
                'form' => $form->createView(),
            ]
        );
    }

    private function treatment(array $data, Volontaire|Association $object)
    {
        $newUser = $data['user'];
        if ($newUser) {
            if ($object instanceof Volontaire) {
                if ($volontaire = $this->volontaireRepository->findVolontaireByUser($newUser)) {
                    $volontaire->user = null;
                    $this->addFlash('warning', 'Le compte a été dissocié de '.$volontaire);
                }
            }
            if ($object instanceof Association) {
                if ($association = $this->associationRepository->findAssociationByUser($newUser)) {
                    $association->user = null;
                    $this->addFlash('success', 'Le compte a été dissocié de '.$association);
                }
            }
        }

        $object->user = $newUser;
        $this->userRepository->flush();
        if ($newUser) {
            $this->addFlash('success', 'Le compte a bien été associé');
        } else {
            $this->addFlash('success', 'Le compte a bien été dissocié');
        }
    }
}
