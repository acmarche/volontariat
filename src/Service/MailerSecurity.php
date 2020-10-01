<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Security\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class MailerSecurity
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var \Swift_Mailer
     */
    private $swiftMailer;
    /**
     * @var string
     */
    private $to;
    /**
     * @var string
     */
    private $from;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        Environment $twig,
        FlashBagInterface $flashBag,
        \Swift_Mailer $swiftMailer,
        ParameterBagInterface $parameterBag,
        RouterInterface $router,
        string $to,
        string $from
    ) {
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->swiftMailer = $swiftMailer;
        $this->to = $to;
        $this->from = $from;
        $this->parameterBag = $parameterBag;
        $this->router = $router;
    }

    public function send($from, $destinataires, $sujet, $body, $bcc = null)
    {
        $mail = (new \Swift_Message($sujet))
            ->setFrom($from)
            ->setTo($destinataires);

        if ($bcc) {
            $mail->setBcc($bcc);
        }

        $mail->setBody($body);

        $this->swiftMailer->send($mail);
    }

    /**
     * Lors de la création du compte
     * @param $user
     */
    public function sendWelcome(User $user)
    {
        $sujet = 'Bienvenue sur la plate-forme du volontariat';
        $body = $this->twig->render('security/registration/email.welcome.txt.twig', array());

        $this->flashBag->add("success", 'Vous êtes bien inscrit');

        try {
            $this->send($this->from, $user->getEmail(), $sujet, $body, $this->from);
        } catch (\Swift_SwiftException $e) {
            $this->flashBag->add("error", $e->getMessage());
        }
    }

    public function sendRequestNewPassword(User $user)
    {
        $body = $this->twig->render(
            'security/resetting/email.txt.twig',
            [
                'user' => $user,
            ]
        );

        $sujet = "Volontariat, demande d'un nouveau mot de passe";

        $this->send($this->from, $user->getEmail(), $sujet, $body);
    }

    public function sendError(string $sujet, string $body)
    {
        $to = "jf@marche.be";

        try {
            $this->send($this->from, $to, $sujet, $body);
        } catch (\Swift_SwiftException $e) {
            $this->flashBag->add("error", $e->getMessage());
        }
    }
}
