<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Security\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailerSecurity
{
    public function __construct(
        private Environment $twig,
        private MailerInterface $mailer,
        private string $from
    ) {

    }

    /**
     * @throws TransportExceptionInterface
     */
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
     * @param User $user
     * @param string $password
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError|TransportExceptionInterface
     */
    public function sendWelcomeVoluntary(User $user, string $password): void
    {
        $sujet = 'Bienvenue sur la plate-forme du volontariat';
        $body = $this->twig->render(
            '@Volontariat/security/registration/email.welcome.txt.twig',
            ['user' => $user, 'password' => $password]
        );

        $this->send($this->from, $user->getEmail(), $sujet, $body, $this->from);
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

        }
    }
}
