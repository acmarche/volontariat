<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\User\ChangePasswordType;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Security\PasswordGenerator;
use AcMarche\Volontariat\Security\RolesEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::admin->value)]
#[Route(path: 'password/users/')]
class PasswordController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordGenerator $passwordGenerator
    ) {
    }

    #[Route(path: '/user/{id}', name: 'volontariat_admin_user_password', methods: ['GET', 'POST'])]
    public function user(Request $request, User $user): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->password = $this->passwordGenerator->cryptPassword($user, $form->getData()->plainPassword);
            $this->userRepository->flush();

            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('volontariat_admin_user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/user/password.html.twig',
            [
                'user' => $user,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/voluntary/{id}', name: 'volontariat_admin_volontaire_password', methods: ['GET', 'POST'])]
    public function voluntary(Request $request, Volontaire $volontaire): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $volontaire);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $volontaire->password = $this->passwordGenerator->cryptPassword(
                $volontaire,
                $form->getData()->plainPassword
            );
            $this->userRepository->flush();

            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('volontariat_admin_volontaire_show', ['id' => $volontaire->getId()]);
        }


        return $this->render(
            '@Volontariat/admin/user/password.html.twig',
            [
                'user' => $volontaire,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/association/{id}', name: 'volontariat_admin_association_password', methods: ['GET', 'POST'])]
    public function association(Request $request, Association $association): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $association);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $association->password = $this->passwordGenerator->cryptPassword(
                $association,
                $form->getData()->plainPassword
            );
            $this->userRepository->flush();

            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('volontariat_admin_association_show', ['id' => $association->getId()]);
        }


        return $this->render(
            '@Volontariat/admin/user/password.html.twig',
            [
                'user' => $association,
                'form' => $form,
            ]
        );
    }

}
