<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Manager\ContactManager;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerContact
{
    /**
     * @var AssociationRepository
     */
    private $associationRepository;
    /**
     * @var VolontaireRepository
     */
    private $volontaireRepository;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var string
     */
    private $from;

    public function __construct(
        AssociationRepository $associationRepository,
        VolontaireRepository $volontaireRepository,
        Environment $twig,
        FlashBagInterface $flashBag,
        MailerInterface $mailer,
        string $from
    ) {
        $this->associationRepository = $associationRepository;
        $this->volontaireRepository = $volontaireRepository;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->mailer = $mailer;
        $this->from = $from;
    }

    public function sendToVolontaire(Volontaire $volontaire, ContactManager $contactManager)
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

    public function sendToAssociation(Association $association, ContactManager $contactManager)
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

    protected function sendCopyVolontariat(ContactManager $contactManager)
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
