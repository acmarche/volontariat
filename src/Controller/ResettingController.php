<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Form\User\LostPasswordType;
use AcMarche\Volontariat\Mailer\MailerSecurity;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'password')]
class ResettingController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private TokenManager $tokenManager,
        private MailerSecurity $mailer
    ) {
    }

    #[Route(path: '/lost', name: 'volontariat_password_lost', methods: ['GET', 'POST'])]
    public function request(Request $request): Response
    {
        $form = $this->createForm(LostPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneByEmail($form->getData()->email);
            if (null === $user) {
                $this->addFlash('danger', 'Aucun utilisateur trouvé avec cette adresse mail');
                sleep(3);

                return $this->redirectToRoute('volontariat_password_lost');
            }

            $token = $this->tokenManager->generate($user);

            try {
                $this->mailer->sendRequestNewPassword($user, $token);
                $this->addFlash('success', 'Un mail avec la procédure à suivre vous a été envoyé');

            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('volontariat_home');
        }

        return $this->render(
            '@Volontariat/security/lost_password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
