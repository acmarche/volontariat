<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Form\User\ChangePasswordType;
use AcMarche\Volontariat\Form\User\UserEditType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\PasswordGenerator;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use AcMarche\Volontariat\Security\RolesEnum;
#[IsGranted(RolesEnum::association->value)]
class UtilisateurController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private PasswordGenerator $passwordGenerator
    ) {
    }

    #[Route(path: '/admin/user/', name: 'volontariat_admin_user', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->userRepository->findBy([], ['name' => 'ASC']);
        foreach ($users as $user) {
            try {
                $user->volontaire = $this->volontaireRepository->findVolontaireByUser($user);
            } catch (NonUniqueResultException $e) {
                dd($user);
            }

            try {
                $user->association = $this->associationRepository->findAssociationByUser($user);
            } catch (NonUniqueResultException $e) {
                dd($user);
            }
        }

        return $this->render(
            '@Volontariat/admin/user/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    #[Route(path: '/admin/user/{id}', name: 'volontariat_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $volontaire = $this->volontaireRepository->findVolontaireByUser($user);
        $association = $this->associationRepository->findAssociationByUser($user);

        return $this->render(
            '@Volontariat/admin/user/show.html.twig',
            [
                'user' => $user,
                'association' => $association,
                'volontaire' => $volontaire,
            ]
        );
    }

    #[Route(path: '/admin/user/{id}/edit', name: 'volontariat_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $editForm = $this->createForm(UserEditType::class, $user)
            ->add('submit', SubmitType::class, ['label' => 'Update']);

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->userRepository->flush();
            $this->addFlash('success', "L'user a bien été modifié");

            return $this->redirectToRoute('volontariat_admin_user');
        }

        return $this->render(
            '@Volontariat/admin/user/edit.html.twig',
            [
                'user' => $user,
                'edit_form' =>  $editForm,
            ]
        );
    }

    #[Route(path: '/admin/user/password/{id}', name: 'volontariat_admin_user_password', methods: ['GET', 'POST'])]
    public function password(Request $request, User $user): Response
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

    #[Route(path: '/admin/user/{id}/delete', name: 'volontariat_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if (($association = $this->associationRepository->findAssociationByUser($user)) instanceof Association) {
                $this->associationRepository->remove($association);
            }

            if (($volontaire = $this->volontaireRepository->findVolontaireByUser($user)) instanceof Volontaire) {
                $this->volontaireRepository->remove($volontaire);
            }

            $this->userRepository->remove($user);
            $this->userRepository->flush();
            $this->addFlash('success', 'L\'utilisateur a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_admin_user');
    }
}
