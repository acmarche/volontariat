<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Form\User\ChangePasswordType;
use AcMarche\Volontariat\Form\User\UserEditPublicType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\PasswordGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/compte')]
#[IsGranted('ROLE_VOLONTARIAT')]
class CompteController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private PasswordGenerator $passwordGenerator
    ) {
    }

    #[Route(path: '/edit', name: 'volontariat_backend_account_edit')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $oldEmail = $user->email;
        $form = $this->createForm(UserEditPublicType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($oldEmail != $data->email) {
                if ($this->userRepository->findOneByEmailAndSkip($data->email, $user)) {
                    $this->addFlash('danger', 'Cette adresse mail est déjà prise vous ne pouvez pas l\'utiliser');

                    return $this->redirectToRoute('volontariat_dashboard');
                }
            }

            $this->userRepository->flush();
            $this->addFlash('success', 'Le compte a été mis à jour');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        return $this->render(
            '@Volontariat/backend/account/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/password', name: 'volontariat_backend_password_edit')]
    public function password(Request $request): Response
    {
        $user = $this->getUser();
        $formProfil = $this->createForm(ChangePasswordType::class, $user);

        $formProfil->handleRequest($request);

        if ($formProfil->isSubmitted() && $formProfil->isValid()) {
            $data = $formProfil->getData();

            $user->password = $this->passwordGenerator->cryptPassword($user, $data->plainPassword);

            $this->userRepository->flush();

            $this->addFlash('success', 'Profil mis à jour');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        return $this->render(
            '@Volontariat/backend/account/password.html.twig',
            [
                'user' => $user,
                'form' => $formProfil->createView(),
            ]
        );
    }

    #[Route(path: '/delete', name: 'volontariat_backend_user_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request): Response
    {
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {

            if ($association = $this->associationRepository->findAssociationByUser($user)) {
                $this->associationRepository->remove($association);
            }
            if ($volontaire = $this->volontaireRepository->findVolontaireByUser($user)) {
                $this->volontaireRepository->remove($volontaire);
            }

            $this->userRepository->remove($user);
            $this->userRepository->flush();
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
}
