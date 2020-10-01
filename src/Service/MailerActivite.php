<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
     * @var \Swift_Mailer
     */
    private $swiftMailer;
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
        \Swift_Mailer $swiftMailer,
        string $to,
        string $from
    ) {
        $this->associationRepository = $associationRepository;
        $this->volontaireRepository = $volontaireRepository;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->messageService = $messageService;
        $this->swiftMailer = $swiftMailer;
        $this->to = $to;
        $this->from = $from;
    }

    public function send($from, $destinataires, $sujet, $body, $bcc = null)
    {
        $mail = (new \Swift_Message($sujet))
            ->setFrom($from)
            ->setTo($destinataires);

        if ($bcc) {
            $mail->setBcc($bcc);
        }

        $mail->setBody($body);

        $this->swiftMailer->send($mail);
    }

    /**
     * L'admin doit valider une activite
     * @param Activite $activite
     * @param User $user
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendRequest(Activite $activite, User $user)
    {
        $sujet = 'Une activite a valider sur la plate-forme du volontariat';

        $body = $this->twig->render(
            'mail/activite/to_validate.html.twig',
            array(
                "activite" => $activite,
                "user" => $user,
            )
        );

        try {
            $this->send($this->from, $this->to, $sujet, $body);
        } catch (\Swift_SwiftException $e) {
            $this->flashBag->add("error", $e->getMessage());
        }
    }

    /**
     * Prévient l'asbl qu'elle a été validée
     * @param Activite $activite
     * @return null
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendFinish(Activite $activite)
    {
        $user = $activite->getUser();
        $sujet = 'Votre activite a été validée sur la plate-forme du volontariat';

        $body = $this->twig->render(
            'mail/activite/validee.html.twig',
            array(
                "activite" => $activite,
            )
        );

        try {
            $this->send($this->from, $user->getEmail(), $sujet, $body);
        } catch (\Swift_SwiftException $e) {
            $this->flashBag->add("error", $e->getMessage());
        }
    }

    /**
     * Tout le monde est prévenu
     * @param Activite $activite
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendNew(Activite $activite)
    {
        $sujet = 'Volontariat : '.$activite->getTitre();
        $volontaires = $this->volontaireRepository->findBy(['valider' => true]);

        foreach ($volontaires as $volontaire) {
            $body = $this->twig->render(
                'mail/activite/new.html.twig',
                array(
                    "activite" => $activite,
                    "volontaire" => $volontaire,
                )
            );

            try {
                $this->send($this->from, $volontaire->getEmail(), $sujet, $body);
            } catch (\Swift_SwiftException $e) {
            }
        }

        $associations = $this->associationRepository->findBy(['valider' => true]);

        foreach ($associations as $association) {
            $body = $this->twig->render(
                'mail/activite/new.html.twig',
                array(
                    "activite" => $activite,
                    "volontaire" => $association,
                )
            );

            try {
                $this->send($this->from, $association->getEmail(), $sujet, $body);
            } catch (\Swift_SwiftException $e) {
            }
        }
    }
}
