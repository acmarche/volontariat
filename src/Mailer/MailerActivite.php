<?php

namespace AcMarche\Volontariat\Mailer;

use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailerActivite
{
    public function __construct(
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private Environment $twig,
        private MailerInterface $mailer,
        private string $to,
        private string $from
    ) {
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
     * L'admin doit valider une activite
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendRequest(Activite $activite, User $user): void
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendFinish(Activite $activite): void
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendNew(Activite $activite): void
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
            } catch (TransportException) {
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
            } catch (TransportException) {
            }
        }
    }
}
