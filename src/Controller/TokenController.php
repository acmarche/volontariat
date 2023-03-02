<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Security\Token;
use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route(path: '/token')]
class TokenController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;
    public function __construct(private TokenManager $tokenManager)
    {
    }
    #[Route(path: '/', name: 'volontariat_token')]
    public function index() : void
    {
        $this->tokenManager->createForAllUsers();
    }
    #[Route(path: '/{value}', name: 'volontariat_token_show')]
    public function show(Request $request, Token $token) : RedirectResponse
    {
        if ($this->tokenManager->isExpired($token)) {
            $this->addFlash('error', "Cette url a expirée");

            return $this->redirectToRoute('volontariat_home');
        }
        $user = $token->getUser();
        $this->tokenManager->loginUser($request, $user, 'main');
        return $this->redirectToRoute('volontariat_dashboard');
    }
}
