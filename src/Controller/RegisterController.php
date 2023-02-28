<?php

namespace AcMarche\Volontariat\Controller;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Form\User\RegisterType;
use AcMarche\Volontariat\Manager\TokenManager;
use AcMarche\Volontariat\Manager\UserManager;
use AcMarche\Volontariat\Service\MailerSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/register')]
class RegisterController extends AbstractController
{
    public function __construct(private UserManager $userManager, private TokenManager $tokenManager, private MailerSecurity $mailer)
    {
    }
    #[Route(path: '/', name: 'volontariat_register', methods: ['GET', 'POST'])]
    public function register(Request $request) : Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->getData()->getEmail();
            if ($this->userManager->findOneByEmail($email) !== null) {
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
