<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Manager\ContactManager;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerContact
{
    public function __construct(private Environment $twig, private MailerInterface $mailer, private string $from)
    {
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
            array(
                "contactManager" => $contactManager,
                "volontaire" => $volontaire,
            )
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
            array(
                "contactManager" => $contactManager,
                "association" => $association,
            )
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
            array(
                "contactManager" => $contactManager,
            )
        );

        $message->text($body);

        $this->mailer->send($message);
    }
}
