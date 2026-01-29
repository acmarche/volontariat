<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Form\User\LostPasswordType;
use AcMarche\Volontariat\Mailer\MailerSecurity;
use AcMarche\Volontariat\Security\EmailUniquenessChecker;
use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

class ResettingController extends AbstractController
{
    public function __construct(
        private EmailUniquenessChecker $emailUniquenessChecker,
        private TokenManager $tokenManager,
        private MailerSecurity $mailerSecurity
    ) {
    }

    #[Route(path: 'password/lost', name: 'volontariat_password_lost', methods: ['GET', 'POST'])]
    public function request(Request $request): Response
    {
        $form = $this->createForm(LostPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->emailUniquenessChecker->findByEmail($email);
            if (!$user) {
                $this->addFlash('danger', 'Aucun utilisateur trouvé avec cette adresse mail');
                sleep(3);

                return $this->redirectToRoute('volontariat_password_lost');
            }

            $token = $this->tokenManager->generate($user);
            if (!$token) {
                $this->addFlash('danger', 'Le lien pour vous connecter n\'a pas pu être généré');

                return $this->redirectToRoute('volontariat_password_lost');
            }

            try {
                $this->mailerSecurity->sendRequestNewPassword($user, $token);
                $this->addFlash('success', 'Un mail avec la procédure à suivre vous a été envoyé');

            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('volontariat_home');
        }

        return $this->render(
            '@Volontariat/security/lost_password.html.twig',
            [
                'form' => $form,
            ]
        );
    }
}
