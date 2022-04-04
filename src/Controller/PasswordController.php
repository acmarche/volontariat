<?php

namespace AcMarche\Volontariat\Controller;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Form\User\UtilisateurPasswordType;
use AcMarche\Volontariat\Manager\PasswordManager;
use AcMarche\Volontariat\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Password controller.
 */
#[Route(path: '/security/password')]
#[IsGranted('ROLE_VOLONTARIAT')]
class PasswordController extends AbstractController
{
    public function __construct(private PasswordManager $passwordManager, private UserRepository $userRepository)
    {
    }
    /**
     * Displays a form to edit an existing Abonnement entity.
     *
     *
     */
    #[Route(path: '/', name: 'volontariat_user_change_password', methods: ['GET', 'POST'])]
    public function edit(Request $request) : Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UtilisateurPasswordType::class, $user)
            ->add('Update', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('plainPassword')->getData();

            $this->passwordManager->changePassword($user, $plainPassword);

            $this->userRepository->save();

            $this->addFlash('success', 'Le mot de passe a bien été modifié.');

            return $this->redirectToRoute('volontariat_dashboard');
        }
        return $this->render(
            '@Volontariat/security/change_password/change_password.html.twig',
            array(
                'user' => $user,
                'form' => $form->createView(),
            )
        );
    }
}
