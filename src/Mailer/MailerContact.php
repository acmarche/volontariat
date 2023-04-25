<?php

namespace AcMarche\Volontariat\Mailer;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Entity\Volontaire;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerContact
{
    use MailerTrait;

    public function __construct(
        private MailerInterface $mailer,
        private string $from
    ) {
    }

    /**
     * Envoie formulaire page contact.
     *
     * @throws TransportExceptionInterface
     */
    public function sendContact(array $data): void
    {
        $email = (new TemplatedEmail())
            ->from($data['email'])
            ->to(new Address($this->from))
            ->subject('Contact sur la plate-forme du volontariat')
            ->htmlTemplate('@Volontariat/emails/_page_contact.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $data,
                ])
            );

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendToVolontaire(Volontaire $volontaire, array $data): void
    {
        $email = (new TemplatedEmail())
            ->from($data['email'])
            ->to(new Address($volontaire->email))
            ->bcc(new Address($this->from))
            ->subject('On vous contact via la plate-forme du volontariat')
            ->htmlTemplate('@Volontariat/emails/_to_volontaire.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $data,
                    'volontaire' => $volontaire,
                ])
            );

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendToAssociation(Association $association, array $data): void
    {
        $email = (new TemplatedEmail())
            ->from($data['email'])
            ->to(new Address($this->from))
            ->bcc(new Address($this->from))
            ->subject('On vous contact via la plate-forme du volontariat')
            ->htmlTemplate('@Volontariat/emails/_to_association.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $data,
                    'association' => $association,
                ])
            );

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendReferencerVolontaire(Association $association, Volontaire $volontaire, Message $data): void
    {
        $email = (new TemplatedEmail())
            ->from($data->from)
            ->to(new Address($volontaire->email))
            ->bcc(new Address($this->from))
            ->subject($association->name.' vous recommande un volontaire')
            ->htmlTemplate('@Volontariat/emails/_recommande_volontaire.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $data,
                    'association' => $association,
                    'volontaire' => $volontaire,
                ])
            );

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendReferencerAssociation(Association $association, Message $data): void
    {
        $email = (new TemplatedEmail())
            ->from($data->from)
            ->to(new Address($data->to))
            ->bcc(new Address($this->from))
            ->subject($data->sujet.' vous recommande une association')
            ->htmlTemplate('@Volontariat/emails/_recommande_association.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $data,
                    'association' => $association,
                ])
            );

        $this->mailer->send($email);
    }
}
