<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Security\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerSecurity
{
    private FlashBagInterface $flashBag;

    public function __construct(
        private Environment $twig,
        RequestStack $requestStack,
        private MailerInterface $mailer,
        private string $to,
        private string $from
    ) {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function send($from, $destinataires, $sujet, $body, $bcc = null): void
    {
        $mail = (new Email())
            ->subject($sujet)
            ->from($from)
            ->to($destinataires);

        if ($bcc) {
            $mail->bcc($bcc);
        }

        $mail->text($body);

        $this->mailer->send($mail);
    }

    /**
     * Lors de la création du compte
     * @param $user
     */
    public function sendWelcome(User $user): void
    {
        $sujet = 'Bienvenue sur la plate-forme du volontariat';
        $body = $this->twig->render('@Volontariat/security/registration/email.welcome.txt.twig', array());

        $this->flashBag->add("success", 'Vous êtes bien inscrit');

        try {
            $this->send($this->from, $user->getEmail(), $sujet, $body, $this->from);
        } catch (TransportException $e) {
            $this->flashBag->add("error", $e->getMessage());
        }
    }

    public function sendRequestNewPassword(User $user): void
    {
        $body = $this->twig->render(
            '@Volontariat/security/resetting/email.txt.twig',
            [
                'user' => $user,
            ]
        );

        $sujet = "Volontariat, demande d'un nouveau mot de passe";

        $this->send($this->from, $user->getEmail(), $sujet, $body);
    }

    public function sendError(string $sujet, string $body): void
    {
        $to = "jf@marche.be";

        try {
            $this->send($this->from, $to, $sujet, $body);
        } catch (TransportException $e) {
            $this->flashBag->add("error", $e->getMessage());
        }
    }
}
