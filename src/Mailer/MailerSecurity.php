<?php

namespace AcMarche\Volontariat\Mailer;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\Token;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailerSecurity
{
    public function __construct(
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
     * Lors de la création du compte.
     *
     * @param Volontaire $voluntary
     * @param string $password
     * @param Token $token
     * @throws TransportExceptionInterface
     */
    public function sendWelcomeVoluntary(Volontaire $voluntary, string $password, Token $token): void
    {
        $email = (new TemplatedEmail())
            ->from($this->from)
            ->to(new Address($voluntary->email))
            ->subject('Bienvenue sur la plate-forme du volontariat')
            ->htmlTemplate('@Volontariat/emails/_welcome_voluntary.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'voluntary' => $voluntary,
                    'password' => $password,
                    'token' => $token->getValue(),
                ])
            );

        $this->mailer->send($email);
    }

    /**
     * Lors de la création du compte.
     *
     * @param Association $association
     * @param string $password
     * @param Token $token
     * @throws TransportExceptionInterface
     */
    public function sendWelcomeAssociation(Association $association, string $password, Token $token)
    {
        $email = (new TemplatedEmail())
                   ->from($this->from)
                   ->to(new Address($association->email))
                   ->subject('Bienvenue sur la plate-forme du volontariat')
                   ->htmlTemplate('@Volontariat/emails/_welcome_association.html.twig')
                   ->context(
                       array_merge($this->defaultParams(), [
                           'association' => $association,
                           'password' => $password,
                           'token' => $token->getValue(),
                       ])
                   );

        $this->mailer->send($email);
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

        $this->send($this->from, $user->email, $sujet, $body);
    }

    public function sendError(string $sujet, string $body): void
    {
        $to = 'jf@marche.be';

        try {
            $this->send($this->from, $to, $sujet, $body);
        } catch (TransportException $e) {
        }
    }

    private function defaultParams(): array
    {
        return [
            'importance' => 'high',
            'content' => '',
            'action_url' => '',
            'action_text' => '',
            'footer_text' => '',
            'markdown' => false,
            'raw' => false,
            'exception' => false,
        ];
    }
}
