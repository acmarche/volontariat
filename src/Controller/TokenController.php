<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Security\Token;
use AcMarche\Volontariat\Form\EmptyType;
use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/token')]
class TokenController extends AbstractController
{
    public function __construct(private TokenManager $tokenManager)
    {
    }

    #[Route(path: '/', name: 'volontariat_token_index')]
    #[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
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
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{value}', name: 'volontariat_token_show')]
    public function show(Request $request, Token $token): RedirectResponse
    {
        if ($this->tokenManager->isExpired($token)) {
            $this->addFlash('error', 'Cette url a expirée');

            return $this->redirectToRoute('volontariat_home');
        }
        $user = $token->getUser();
        $this->tokenManager->loginUser($request, $user, 'main');

        return $this->redirectToRoute('volontariat_dashboard');
    }
}
