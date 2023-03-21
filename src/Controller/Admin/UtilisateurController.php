<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Form\User\ChangePasswordType;
use AcMarche\Volontariat\Form\User\UtilisateurEditType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\PasswordGenerator;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/user')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class UtilisateurController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private PasswordGenerator $passwordGenerator
    ) {
    }

    #[Route(path: '/', name: 'volontariat_admin_user', methods: ['GET'])]
    public function indexAction(): Response
    {
        $users = $this->userRepository->findBy([], ['name' => 'ASC']);
        foreach ($users as $user) {
            try {
                $user->volontaire = $this->volontaireRepository->findVolontaireByUser($user);
            } catch (NonUniqueResultException $e) {
                dd($user);
            }
            $user->association = $this->associationRepository->findAssociationByUser($user);
        }

        return $this->render(
            '@Volontariat/admin/user/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'volontariat_admin_user_show', methods: ['GET'])]
    public function showAction(User $user): Response
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

    #[Route(path: '/{id}/edit', name: 'volontariat_admin_user_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, User $user): Response
    {
        $editForm = $this->createForm(UtilisateurEditType::class, $user)
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
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'volontariat_admin_association_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $volontaires = $this->volontaireRepository->findBy(['user' => $user]);
            foreach ($volontaires as $volontaire) {
                $this->volontaireRepository->remove($volontaire);
            }

            $associations = $this->associationRepository->findBy(['user' => $user]);

            foreach ($associations as $association) {
                $this->associationRepository->remove($association);
            }

            $this->userRepository->remove($user);
            $this->addFlash('success', 'L\'utilisateur a bien été supprimé');
        }

        return $this->redirectToRoute('volontariat_admin_user');
    }

    #[Route(path: '/password/{id}', name: 'volontariat_admin_user_password', methods: ['GET', 'POST'])]
    public function password(Request $request, User $user): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->password = $this->passwordGenerator->cryptPassword($user, $form->getData()->getPlainPassword());
            $this->userRepository->flush();

            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('volontariat_admin_user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/user/password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
}
