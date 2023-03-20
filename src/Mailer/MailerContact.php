<?php

namespace AcMarche\Volontariat\Mailer;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Manager\ContactManager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

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

    public function sendToVolontaire(Volontaire $volontaire, ContactManager $contactManager): void
    {
        $message = (new Email())
            ->subject($contactManager->getSujet())
            ->from($contactManager->getEmail())
            ->to($volontaire->getEmail());

        $message->cc($contactManager->getEmail());

        $body = $this->twig->render(
            '@Volontariat/contact/_mail_volontaire.html.twig',
            [
                'contactManager' => $contactManager,
                'volontaire' => $volontaire,
            ]
        );

        $message->text($body);

        $this->mailer->send($message);

        $this->sendCopyVolontariat($contactManager);
    }

    public function sendToAssociation(Association $association, ContactManager $contactManager): void
    {
        $message = (new Email())
            ->subject($contactManager->getSujet())
            ->from($contactManager->getEmail())
            ->to($association->getEmail());

        $message->cc($contactManager->getEmail());

        $body = $this->twig->render(
            '@Volontariat/contact/_mail_association.html.twig',
            [
                'contactManager' => $contactManager,
                'association' => $association,
            ]
        );

        $message->text($body);

        $this->mailer->send($message);

        $this->sendCopyVolontariat($contactManager);
    }

    protected function sendCopyVolontariat(ContactManager $contactManager): void
    {
        $message = (new Email())
            ->subject($contactManager->getSujet())
            ->from($this->from)
            ->to($this->from);

        $body = $this->twig->render(
            '@Volontariat/contact/_mail_copy.html.twig',
            [
                'contactManager' => $contactManager,
            ]
        );

        $message->text($body);

        $this->mailer->send($message);
    }
}
