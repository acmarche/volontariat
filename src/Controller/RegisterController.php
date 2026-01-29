<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Association\Form\RegisterAssociationType;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Mailer\MailerSecurity;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\EmailUniquenessChecker;
use AcMarche\Volontariat\Security\PasswordGenerator;
use AcMarche\Volontariat\Security\TokenManager;
use AcMarche\Volontariat\Voluntary\Form\RegisterVoluntaryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RegisterController extends AbstractController
{
    public function __construct(
        private VolontaireRepository $volontaireRepository,
        private AssociationRepository $associationRepository,
        private TokenManager $tokenManager,
        private MailerSecurity $mailerSecurity,
        private PasswordGenerator $passwordGenerator,
        private EmailUniquenessChecker $emailUniquenessChecker,
    ) {
    }

    #[Route(path: '/register/', name: 'volontariat_register_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('@Volontariat/registration/index.html.twig');
    }

    #[Route(path: '/register/voluntary', name: 'volontariat_register_voluntary', methods: ['GET', 'POST'])]
    public function registerVoluntary(Request $request): Response
    {
        $volontaire = new Volontaire();
        $form = $this->createForm(RegisterVoluntaryType::class, $volontaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()->email;
            if (!$this->emailUniquenessChecker->isEmailAvailable($email)) {
                $this->addFlash('danger', 'Un compte est déjà inscrit avec cette adresse email');

                return $this->redirectToRoute('volontariat_register_voluntary');
            }

            $plainPassword = $this->passwordGenerator->generate();
            $volontaire->password = $this->passwordGenerator->cryptPassword($volontaire, $plainPassword);
            $volontaire->setUuid($volontaire->generateUuid());
            $this->volontaireRepository->insert($volontaire);

            $tokenValue = $this->tokenManager->generate($volontaire);

            try {
                $this->mailerSecurity->sendWelcomeVoluntary($volontaire, $plainPassword, $tokenValue);
            } catch (TransportException|TransportExceptionInterface $e) {
                $this->addFlash('danger', $e->getMessage());
            }

            return $this->redirectToRoute('volontariat_register_complete');
        }

        return $this->render(
            '@Volontariat/registration/register_voluntary.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/register/association', name: 'volontariat_register_association', methods: ['GET', 'POST'])]
    public function registerAssociation(Request $request): Response
    {
        $association = new Association();
        $association->setUuid($association->generateUuid());

        $form = $this->createForm(RegisterAssociationType::class, $association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()->email;
            if (!$this->emailUniquenessChecker->isEmailAvailable($email)) {
                $this->addFlash('danger', 'Un compte est déjà inscrit avec cette adresse email');

                return $this->redirectToRoute('volontariat_register_association');
            }

            $plainPassword = $this->passwordGenerator->generate();
            $association->password = $this->passwordGenerator->cryptPassword($association, $plainPassword);
            $this->associationRepository->insert($association);

            $tokenValue = $this->tokenManager->generate($association);

            try {
                $this->mailerSecurity->sendWelcomeAssociation($association, $plainPassword, $tokenValue);
                $this->addFlash('success', 'Votre Asbl a bien été inscrite');
                $this->addFlash('warning', 'Votre Asbl doit être validée par un administrateur');
            } catch (TransportException|LoaderError|RuntimeError|SyntaxError|TransportExceptionInterface $e) {
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToRoute('volontariat_register_complete');
        }

        return $this->render(
            '@Volontariat/registration/register_association.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/register/complete', name: 'volontariat_register_complete', methods: ['GET'])]
    public function complete(): Response
    {
        return $this->render(
            '@Volontariat/registration/complete.html.twig',
            [
            ]
        );
    }
}
