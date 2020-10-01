<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Form\User\ChangePasswordType;
use AcMarche\Volontariat\Form\User\UtilisateurEditType;
use AcMarche\Volontariat\Form\User\UtilisateurType;
use AcMarche\Volontariat\Manager\PasswordManager;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/utilisateur")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var AssociationRepository
     */
    private $associationRepository;
    /**
     * @var VolontaireRepository
     */
    private $volontaireRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var PasswordManager
     */
    private $passwordManager;

    public function __construct(
        UserRepository $userRepository,
        AssociationRepository $associationRepository,
        VolontaireRepository $volontaireRepository,
        PasswordManager $passwordManager
    ) {
        $this->userRepository = $userRepository;
        $this->associationRepository = $associationRepository;
        $this->volontaireRepository = $volontaireRepository;
        $this->passwordManager = $passwordManager;
    }

    /**
     * Lists all Utilisateur entities.
     *
     * @Route("/", name="volontariat_admin_utilisateur", methods={"GET"})
     *
     */
    public function indexAction()
    {
        $users = $this->userRepository->findBy([], ['nom' => 'ASC']);
        foreach ($users as $user) {
            $user->setCountVolontaires(count($this->volontaireRepository->findBy(['user' => $user])));
            $user->setCountAssociations(count($this->associationRepository->findBy(['user' => $user])));
        }

        return $this->render(
            '@Volontariat/admin/utilisateur/index.html.twig',
            array(
                'users' => $users,
            )
        );
    }

    /**
     * Displays a form to create a new Utilisateur utilisateur.
     *
     * @Route("/new", name="volontariat_admin_utilisateur_new", methods={"GET","POST"})
     *
     */
    public function newAction(Request $request)
    {
        $utilisateur = new User();

        $form = $this->createForm(UtilisateurType::class, $utilisateur)
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->insert($utilisateur);

            $this->addFlash("success", "L'utilisateur a bien été ajouté");

            return $this->redirectToRoute('volontariat_admin_utilisateur');
        }

        return $this->render(
            '@Volontariat/admin/utilisateur/new.html.twig',
            array(
                'utilisateur' => $utilisateur,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Utilisateur utilisateur.
     *
     * @Route("/{id}", name="volontariat_admin_utilisateur_show", methods={"GET"})
     *
     */
    public function showAction(User $utilisateur)
    {
        $deleteForm = $this->createDeleteForm($utilisateur);
        $volontaires = $this->volontaireRepository->search(['user' => $utilisateur]);

        $associations = $this->associationRepository->search(['user' => $utilisateur]);

        return $this->render(
            '@Volontariat/admin/utilisateur/show.html.twig',
            array(
                'utilisateur' => $utilisateur,
                'associations' => $associations,
                'volontaires' => $volontaires,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Utilisateur utilisateur.
     *
     * @Route("/{id}/edit", name="volontariat_admin_utilisateur_edit", methods={"GET","POST"})
     *
     */
    public function editAction(Request $request, User $utilisateur)
    {
        $editForm = $this->createForm(UtilisateurEditType::class, $utilisateur)
            ->add('submit', SubmitType::class, array('label' => 'Update'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->userRepository->save();
            $this->addFlash("success", "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('volontariat_admin_utilisateur');
        }

        return $this->render(
            '@Volontariat/admin/utilisateur/edit.html.twig',
            array(
                'utilisateur' => $utilisateur,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Utilisateur utilisateur.
     *
     * @Route("/{id}", name="volontariat_admin_utilisateur_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, User $user)
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

            $this->addFlash("success", "L'utilisateur a bien été supprimé");
        }

        return $this->redirectToRoute('volontariat_admin_utilisateur');
    }

    /**
     * Displays a form to edit an existing categorie entity.
     *
     * @Route("/password/{id}", name="volontariat_admin_utilisateur_password", methods={"GET","POST"})
     *
     */
    public function password(Request $request, User $user)
    {
        $form = $this->createForm(ChangePasswordType::class, $user)
            ->add('submit', SubmitType::class, ['label' => 'Valider']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->passwordManager->changePassword($user, $form->getData()->getPlainPassword());
            $this->userRepository->save();

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

    /**
     * Creates a form to delete a Utilisateur utilisateur by id.
     *
     * @param mixed $id The utilisateur id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_utilisateur_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
