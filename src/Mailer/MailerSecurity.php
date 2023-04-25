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

class MailerSecurity
{
    use MailerTrait;

    public function __construct(
        private MailerInterface $mailer,
        private string $from
    ) {
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
            ->bcc(new Address($this->from))
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
            ->bcc(new Address($this->from))
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

    /**
     * @param User $user
     * @param Token $token
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendRequestNewPassword(User $user, Token $token): void
    {
        $email = (new TemplatedEmail())
            ->from($this->from)
            ->to(new Address($user->email))
            ->subject('Lien de connection sur la plate-forme du volontariat')
            ->htmlTemplate('@Volontariat/emails/_lost_password.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'user' => $user,
                    'token' => $token->getValue(),
                ])
            );

        $this->mailer->send($email);
    }
}
