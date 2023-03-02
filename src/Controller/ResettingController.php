<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Form\User\LostPasswordType;
use AcMarche\Volontariat\Form\User\ResettingFormType;
use AcMarche\Volontariat\Manager\TokenManager;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Service\MailerSecurity;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'password/lost')]
class ResettingController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private TokenManager $tokenManager,
        private MailerSecurity $mailer
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/', name: 'volontariat_password_lost', methods: ['GET', 'POST'])]
    public function request(Request $request): Response
    {
        $form = $this->createForm(LostPasswordType::class)
            ->add('submit', SubmitType::class, ['label' => 'Demander un nouveau mot de passe']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->userRepository->findOneBy(['email' => $form->getData()->getEmail()]);
            if ($user === null) {
                $this->addFlash('warning', 'Aucun utilisateur trouvé');

                return $this->redirectToRoute('volontariat_password_lost');
            }
            $token = $this->generateToken();
            $user->setConfirmationToken($token);
            $this->userRepository->save();
            $this->mailer->sendRequestNewPassword($user);

            return $this->redirectToRoute('volontariat_password_confirmation');
        }

        return $this->render(
            '@Volontariat/security/resetting/request.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/confirmation', name: 'volontariat_password_confirmation', methods: ['GET'])]
    public function requestConfirmed(): Response
    {
        return $this->render(
            '@Volontariat/security/resetting/confirmed.html.twig'
        );
    }

    /**
     * Reset user password.
     * @param string $token
     *
     */
    #[Route(path: '/reset/{token}', name: 'volontariat_password_reset', methods: ['GET', 'POST'])]
    public function reset(Request $request, $token): Response
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $token]);
        if (!$user instanceof User) {
            $this->addFlash('warning', 'Jeton non trouvé');

            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ResettingFormType::class, $user)
            ->add('submit', SubmitType::class, ['label' => 'Valider']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->passwordManager->changePassword($user, $form->getData()->getPlainPassword());

            $user->setConfirmationToken(null);

            $this->userRepository->save();

            $this->addFlash('success', 'Votre mot de passe a bien été changé');

            $this->tokenManager->loginUser($request, $user, 'main');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        return $this->render(
            '@Volontariat/security/resetting/reset.html.twig',
            array(
                'token' => $token,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @throws Exception
     */
    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
