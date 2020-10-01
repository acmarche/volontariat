<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Form\User\RegisterType;
use AcMarche\Volontariat\Manager\TokenManager;
use AcMarche\Volontariat\Manager\UserManager;
use AcMarche\Volontariat\Service\MailerSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegisterController
 * @package AcMarche\Admin\Security\Controller
 * @Route("/register")
 */
class RegisterController extends AbstractController
{
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

    public function __construct(
        UserManager $userManager,
        TokenManager $tokenManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        MailerSecurity $mailer
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->mailer = $mailer;
        $this->userManager = $userManager;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @Route("/", name="volontariat_register", methods={"GET","POST"})
     */
    public function register(Request $request)
    {
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->getData()->getEmail();
            if ($this->userManager->findOneByEmail($email)) {
                $this->addFlash('danger', 'Un utilisateur a déjà cet email');

                return $this->redirectToRoute('volontariat_register');
            }

            $this->userManager->insert($user);

            $this->mailer->sendWelcome($user);

            $this->tokenManager->loginUser($request, $user, 'main');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        return $this->render(
            '@Volontariat/security/registration/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
