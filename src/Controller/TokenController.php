<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Security\Token;
use AcMarche\Volontariat\Manager\TokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class TokenController
 * @package AcMarche\Volontariat\Controller
 * @Route("/token")
 */
class TokenController extends AbstractController
{
    /**
     * @var TokenManager
     */
    private $tokenManager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * @Route("/",name="volontariat_token")
     *
     */
    public function index()
    {
        $this->tokenManager->createForAllUsers();
    }

    /**
     * @Route("/{value}",name="volontariat_token_show")
     *
     */
    public function show(Request $request, Token $token)
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
