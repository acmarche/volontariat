<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Form\EmptyType;
use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use AcMarche\Volontariat\Security\RolesEnum;

class TokenController extends AbstractController
{
    public function __construct(private TokenManager $tokenManager)
    {
    }

    #[Route(path: '/token/', name: 'volontariat_token_index')]
    #[IsGranted(RolesEnum::admin->value)]
    public function index(Request $request): Response
    {
        $form = $this->createForm(EmptyType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tokenManager->createForAllUsers();
            $this->addFlash('success', 'Token créé pour tout le monde');
        }

        return $this->render(
            '@Volontariat/user/token.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/token/{value}', name: 'volontariat_token_show')]
    public function show(Request $request, string $value): RedirectResponse
    {
        $user = $this->tokenManager->findByTokenValue($value);

        if (!$user) {
            $this->addFlash('danger', 'Token invalide');

            return $this->redirectToRoute('volontariat_home');
        }

        if ($this->tokenManager->isExpired($user)) {
            $this->addFlash('danger', 'Cette url a expirée');

            return $this->redirectToRoute('volontariat_home');
        }

        $this->tokenManager->loginUser($request, $user, 'main');

        return $this->redirectToRoute('volontariat_dashboard');
    }
}
