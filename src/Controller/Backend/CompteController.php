<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\User\ChangePasswordType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\PasswordGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CompteController extends AbstractController
{
    public function __construct(
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private PasswordGenerator $passwordGenerator,
    ) {
    }

    #[Route(path: '/compte/password', name: 'volontariat_backend_password_edit')]
    public function password(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->password = $this->passwordGenerator->cryptPassword($user, $plainPassword);

            $this->flushEntity();

            $this->addFlash('success', 'Profil mis à jour');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        return $this->render(
            '@Volontariat/backend/account/password.html.twig',
            [
                'user' => $user,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/compte/delete', name: 'volontariat_backend_user_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request): Response
    {
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if ($user instanceof Association) {
                $this->associationRepository->remove($user);
                $this->associationRepository->flush();
            } elseif ($user instanceof Volontaire) {
                $this->volontaireRepository->remove($user);
                $this->volontaireRepository->flush();
            }

            // Invalidate session
            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken(null);

            $this->addFlash('success', 'Le compte a bien été supprimé');

            return $this->redirectToRoute('volontariat_home');
        }

        return $this->render(
            '@Volontariat/backend/account/delete.html.twig',
            [
                'user' => $user,
            ]
        );
    }

    private function flushEntity(): void
    {
        $this->associationRepository->flush();
    }
}
