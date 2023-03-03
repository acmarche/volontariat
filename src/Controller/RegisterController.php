<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\User\RegisterVoluntaryType;
use AcMarche\Volontariat\Mailer\MailerSecurity;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\PasswordGenerator;
use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route(path: '/register')]
class RegisterController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private VolontaireRepository $volontaireRepository,
        private TokenManager $tokenManager,
        private MailerSecurity $mailer,
        private PasswordGenerator $passwordGenerator,
    ) {
    }
#[Route(path: '/', name: 'volontariat_register_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@Volontariat/security/registration/index.html.twig',
            [

            ]
        );
    }

    #[Route(path: '/voluntary', name: 'volontariat_register_voluntary', methods: ['GET', 'POST'])]
    public function registerVoluntary(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterVoluntaryType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->getData()->getEmail();
            if ($this->userRepository->findOneByEmail($email) !== null) {
                $this->addFlash('danger', 'Un volontaire a déjà cette adresse email');

                return $this->redirectToRoute('volontariat_register_voluntary');
            }

            $plainPassword = $this->passwordGenerator->generate();
            $user->setPassword($this->passwordGenerator->cryptPassword($user, $plainPassword));
            $this->userRepository->insert($user);

            $voluntary = Volontaire::newFromUser($user);
            $this->volontaireRepository->insert($voluntary);

            try {
                $this->mailer->sendWelcomeVoluntary($user, $plainPassword);
                $this->addFlash("success", 'Vous êtes bien inscrit');
            } catch (TransportException|LoaderError|RuntimeError|SyntaxError|TransportExceptionInterface $e) {
                $this->addFlash("error", $e->getMessage());
            }

            $this->tokenManager->loginUser($request, $user, 'main');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        return $this->render(
            '@Volontariat/security/registration/register_voluntary.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/association', name: 'volontariat_register_association', methods: ['GET', 'POST'])]
    public function registerAssociation(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterAssociationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->getData()->getEmail();
            if ($this->userRepository->findOneByEmail($email) !== null) {
                $this->addFlash('danger', 'Un volontaire a déjà cette adresse email');

                return $this->redirectToRoute('volontariat_register_association');
            }

            $plainPassword = $this->passwordGenerator->generate();
            $user->setPassword($this->passwordGenerator->cryptPassword($user, $plainPassword));
            $this->userRepository->insert($user);

            $voluntary = Volontaire::newFromUser($user);
            $this->volontaireRepository->insert($voluntary);

            try {
                $this->mailer->sendWelcomeVoluntary($user, $plainPassword);
                $this->addFlash("success", 'Vous êtes bien inscrit');
            } catch (TransportException|LoaderError|RuntimeError|SyntaxError|TransportExceptionInterface $e) {
                $this->addFlash("error", $e->getMessage());
            }

            $this->tokenManager->loginUser($request, $user, 'main');

            return $this->redirectToRoute('volontariat_dashboard');
        }

        return $this->render(
            '@Volontariat/security/registration/register_voluntary.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
