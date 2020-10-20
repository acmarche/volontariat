<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerActivite
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
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var MessageService
     */
    private $messageService;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var string
     */
    private $to;
    /**
     * @var string
     */
    private $from;

    public function __construct(
        AssociationRepository $associationRepository,
        VolontaireRepository $volontaireRepository,
        Environment $twig,
        FlashBagInterface $flashBag,
        MessageService $messageService,
        MailerInterface $mailer,
        string $to,
        string $from
    ) {
        $this->associationRepository = $associationRepository;
        $this->volontaireRepository = $volontaireRepository;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->messageService = $messageService;
        $this->mailer = $mailer;
        $this->to = $to;
        $this->from = $from;
    }

    public function send($from, $destinataires, $sujet, $body, $bcc = null)
    {
        $mail = (new Email())
            ->subject($sujet)
            ->setFrom($from)
            ->setTo($destinataires);

        if ($bcc) {
            $mail->bcc($bcc);
        }

        $mail->text($body);

        $this->mailer->send($mail);
    }

    /**
     * L'admin doit valider une activite
     * @param Activite $activite
     * @param User $user
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendRequest(Activite $activite, User $user)
    {
        $sujet = 'Une activite a valider sur la plate-forme du volontariat';

        $body = $this->twig->render(
            '@Volontariat/mail/activite/to_validate.html.twig',
            array(
                "activite" => $activite,
                "user" => $user,
            )
        );

        try {
            $this->send($this->from, $this->to, $sujet, $body);
        } catch (TransportException $e) {
            $this->flashBag->add("error", $e->getMessage());
        }
    }

    /**
     * Prévient l'asbl qu'elle a été validée
     * @param Activite $activite
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendFinish(Activite $activite)
    {
        $user = $activite->getUser();
        $sujet = 'Votre activite a été validée sur la plate-forme du volontariat';

        $body = $this->twig->render(
            '@Volontariat/mail/activite/validee.html.twig',
            array(
                "activite" => $activite,
            )
        );

        try {
            $this->send($this->from, $user->getEmail(), $sujet, $body);
        } catch (TransportException $e) {
            $this->flashBag->add("error", $e->getMessage());
        }
    }

    /**
     * Tout le monde est prévenu
     * @param Activite $activite
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendNew(Activite $activite)
    {
        $sujet = 'Volontariat : '.$activite->getTitre();
        $volontaires = $this->volontaireRepository->findBy(['valider' => true]);

        foreach ($volontaires as $volontaire) {
            $body = $this->twig->render(
                '@Volontariat/mail/activite/new.html.twig',
                array(
                    "activite" => $activite,
                    "volontaire" => $volontaire,
                )
            );

            try {
                $this->send($this->from, $volontaire->getEmail(), $sujet, $body);
            } catch (TransportException $e) {
            }
        }

        $associations = $this->associationRepository->findBy(['valider' => true]);

        foreach ($associations as $association) {
            $body = $this->twig->render(
                '@Volontariat/mail/activite/new.html.twig',
                array(
                    "activite" => $activite,
                    "volontaire" => $association,
                )
            );

            try {
                $this->send($this->from, $association->getEmail(), $sujet, $body);
            } catch (TransportException $e) {
            }
        }
    }
}
