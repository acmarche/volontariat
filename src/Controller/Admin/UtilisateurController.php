<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Form\User\ChangePasswordType;
use AcMarche\Volontariat\Form\User\UtilisateurEditType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Voluntary\Form\RegisterVoluntaryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/utilisateur')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class UtilisateurController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
    ) {
    }

    #[Route(path: '/', name: 'volontariat_admin_utilisateur', methods: ['GET'])]
    public function indexAction(): Response
    {
        $users = $this->userRepository->findBy([], ['name' => 'ASC']);

        return $this->render(
            '@Volontariat/admin/utilisateur/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    #[Route(path: '/new', name: 'volontariat_admin_utilisateur_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request): Response
    {
        $utilisateur = new User();
        $form = $this->createForm(RegisterVoluntaryType::class, $utilisateur)
            ->add('submit', SubmitType::class, ['label' => 'Create']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->insert($utilisateur);

            $this->addFlash('success', "L'utilisateur a bien été ajouté");

            return $this->redirectToRoute('volontariat_admin_utilisateur');
        }

        return $this->render(
            '@Volontariat/admin/utilisateur/new.html.twig',
            [
                'utilisateur' => $utilisateur,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'volontariat_admin_utilisateur_show', methods: ['GET'])]
    public function showAction(User $utilisateur): Response
    {
        $deleteForm = $this->createDeleteForm($utilisateur);
        $volontaires = $this->volontaireRepository->search(['user' => $utilisateur]);
        $associations = $this->associationRepository->search(['user' => $utilisateur]);

        return $this->render(
            '@Volontariat/admin/utilisateur/show.html.twig',
            [
                'utilisateur' => $utilisateur,
                'associations' => $associations,
                'volontaires' => $volontaires,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'volontariat_admin_utilisateur_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, User $utilisateur): Response
    {
        $editForm = $this->createForm(UtilisateurEditType::class, $utilisateur)
            ->add('submit', SubmitType::class, ['label' => 'Update']);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->userRepository->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('volontariat_admin_utilisateur');
        }

        return $this->render(
            '@Volontariat/admin/utilisateur/edit.html.twig',
            [
                'utilisateur' => $utilisateur,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'volontariat_admin_utilisateur_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, User $user): RedirectResponse
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $volontaires = $this->volontaireRepository->findBy(['user' => $user]);
            foreach ($volontaires as $volontaire) {
                $this->volontaireRepository->remove($volontaire);
            }

            $associations = $this->associationRepository->findBy(['user' => $user]);

            foreach ($associations as $association) {
                $this->associationRepository->remove($association);
            }

            $this->userRepository->remove($user);

            $this->addFlash('success', "L'utilisateur a bien été supprimé");
        }

        return $this->redirectToRoute('volontariat_admin_utilisateur');
    }

    #[Route(path: '/password/{id}', name: 'volontariat_admin_utilisateur_password', methods: ['GET', 'POST'])]
    public function password(Request $request, User $user): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->passwordManager->changePassword($user, $form->getData()->getPlainPassword());
            $this->userRepository->flush();

            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('volontariat_admin_utilisateur_show', ['id' => $user->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/utilisateur/password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    private function createDeleteForm(User $user): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_utilisateur_delete', ['id' => $user->getId()]))
            ->setMethod(Request::METHOD_DELETE)
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }
}
