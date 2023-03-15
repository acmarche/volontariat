<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Association\Form\RegisterAssociationType;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Mailer\MailerSecurity;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\PasswordGenerator;
use AcMarche\Volontariat\Security\TokenManager;
use AcMarche\Volontariat\Voluntary\Form\RegisterVoluntaryType;
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
        private AssociationRepository $associationRepository,
        private TokenManager $tokenManager,
        private MailerSecurity $mailer,
        private PasswordGenerator $passwordGenerator,
    ) {
    }

    #[Route(path: '/', name: 'volontariat_register_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@Volontariat/registration/index.html.twig',
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
            if (null !== $this->userRepository->findOneByEmail($email)) {
                $this->addFlash('danger', 'Un volontaire est déjà inscrit avec cette adresse email');

                return $this->redirectToRoute('volontariat_register_voluntary');
            }

            $plainPassword = $this->passwordGenerator->generate();
            $user->password = $this->passwordGenerator->cryptPassword($user, $plainPassword);
            $this->userRepository->insert($user);

            $voluntary = Volontaire::newFromUser($user);
            $this->volontaireRepository->insert($voluntary);

            $token = $this->tokenManager->generate($user);

            try {
                $this->mailer->sendWelcomeVoluntary($user, $plainPassword, $token);
                $this->addFlash('success', 'Vous êtes bien inscrit');
            } catch (TransportException|LoaderError|RuntimeError|SyntaxError|TransportExceptionInterface $e) {
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToRoute('volontariat_register_complete');
        }

        return $this->render(
            '@Volontariat/registration/register_voluntary.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/association', name: 'volontariat_register_association', methods: ['GET', 'POST'])]
    public function registerAssociation(Request $request): Response
    {
        $association = new Association();
        $form = $this->createForm(RegisterAssociationType::class, $association);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()->getEmail();
            if (null !== $this->userRepository->findOneByEmail($email)) {
                $this->addFlash('danger', 'Une association est déjà inscrite avec cette adresse email');

                return $this->redirectToRoute('volontariat_register_association');
            }

            $user = User::createFromAssociation($association);
            $plainPassword = $this->passwordGenerator->generate();
            $user->password = $this->passwordGenerator->cryptPassword($user, $plainPassword);
            $this->userRepository->insert($user);

            $this->associationRepository->insert($association);

            $token = $this->tokenManager->generate($user);

            try {
                $this->mailer->sendWelcomeAssociation($association, $plainPassword, $token);
                $this->addFlash('success', 'Vous êtes bien inscrit');
            } catch (TransportException|LoaderError|RuntimeError|SyntaxError|TransportExceptionInterface $e) {
                $this->addFlash('error', $e->getMessage());
            }

            // $this->tokenManager->loginUser($request, $association, 'main');

            return $this->redirectToRoute('volontariat_register_complete');
        }

        return $this->render(
            '@Volontariat/registration/register_association.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/complete', name: 'volontariat_register_complete', methods: ['GET'])]
    public function complete(): Response
    {
        return $this->render(
            '@Volontariat/registration/complete.html.twig',
            [
            ]
        );
    }
}
