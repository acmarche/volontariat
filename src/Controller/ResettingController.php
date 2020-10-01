<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Form\User\LostPasswordType;
use AcMarche\Volontariat\Form\User\ResettingFormType;
use AcMarche\Volontariat\Manager\PasswordManager;
use AcMarche\Volontariat\Manager\TokenManager;
use AcMarche\Volontariat\Manager\UserManager;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Service\Mailer;
use AcMarche\Volontariat\Service\MailerSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegisterController
 * @package AcMarche\Admin\Security\Controller
 * @Route("password/lost")
 */
class ResettingController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var MailerSecurity
     */
    private $mailer;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var TokenManager
     */
    private $tokenManager;
    /**
     * @var PasswordManager
     */
    private $passwordManager;

    public function __construct(
        UserRepository $userRepository,
        PasswordManager $passwordManager,
        TokenManager $tokenManager,
        MailerSecurity $mailer
    ) {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->tokenManager = $tokenManager;
        $this->passwordManager = $passwordManager;
    }


    /**
     * @param Request $request
     * @Route("/", name="volontariat_password_lost", methods={"GET", "POST"})
     * @return Response
     * @throws \Exception
     */
    public function request(Request $request)
    {
        $form = $this->createForm(LostPasswordType::class)
            ->add('submit', SubmitType::class, ['label' => 'Demander un nouveau mot de passe']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->userRepository->findOneBy(['email' => $form->getData()->getEmail()]);
            if (!$user) {
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

    /**
     * @Route("/confirmation", name="volontariat_password_confirmation", methods={"GET"})
     * @return Response
     */
    public function requestConfirmed()
    {
        return $this->render(
            '@Volontariat/security/resetting/confirmed.html.twig'
        );
    }

    /**
     * Reset user password.
     * @Route("/reset/{token}", name="volontariat_password_reset", methods={"GET","POST"})
     * @param Request $request
     * @param string $token
     *
     * @return Response
     */
    public function reset(Request $request, $token)
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $token]);

        if (null === $user) {
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
     * @return string
     * @throws \Exception
     */
    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
