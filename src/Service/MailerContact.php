<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Manager\ContactManager;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
     * @var \Swift_Mailer
     */
    private $swiftMailer;
    /**
     * @var string
     */
    private $from;

    public function __construct(
        AssociationRepository $associationRepository,
        VolontaireRepository $volontaireRepository,
        Environment $twig,
        FlashBagInterface $flashBag,
        \Swift_Mailer $swiftMailer,
        string $from
    ) {
        $this->associationRepository = $associationRepository;
        $this->volontaireRepository = $volontaireRepository;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->swiftMailer = $swiftMailer;
        $this->from = $from;
    }

    public function sendToVolontaire(Volontaire $volontaire, ContactManager $contactManager)
    {
        $message = (new \Swift_Message($contactManager->getSujet()))
            ->setFrom($contactManager->getEmail())
            ->setTo($volontaire->getEmail());

        $message->setCc($contactManager->getEmail());

        $body = $this->twig->render(
            '@Volontariat/contact/_mail_volontaire.html.twig',
            array(
                "contactManager" => $contactManager,
                "volontaire" => $volontaire,
            )
        );

        $message->setBody($body);

        $this->swiftMailer->send($message);

        $this->sendCopyVolontariat($contactManager);
    }

    public function sendToAssociation(Association $association, ContactManager $contactManager)
    {
        $message = (new \Swift_Message($contactManager->getSujet()))
            ->setFrom($contactManager->getEmail())
            ->setTo($association->getEmail());

        $message->setCc($contactManager->getEmail());

        $body = $this->twig->render(
            '@Volontariat/contact/_mail_association.html.twig',
            array(
                "contactManager" => $contactManager,
                "association" => $association,
            )
        );

        $message->setBody($body, 'text/plain');

        $this->swiftMailer->send($message);

        $this->sendCopyVolontariat($contactManager);
    }

    protected function sendCopyVolontariat(ContactManager $contactManager)
    {
        $message = (new \Swift_Message($contactManager->getSujet()))
            ->setFrom($this->from)
            ->setTo($this->from);

        $body = $this->twig->render(
            '@Volontariat/contact/_mail_copy.html.twig',
            array(
                "contactManager" => $contactManager,
            )
        );

        $message->setBody($body, 'text/plain');

        $this->swiftMailer->send($message);
    }
}
