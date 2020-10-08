<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class Mailer
{
    private $twig;
    private $session;
    private $messageService;
    private $swiftMailer;
    private $to;
    private $from;
    /**
     * @var AssociationRepository
     */
    private $associationRepository;
    /**
     * @var VolontaireRepository
     */
    private $volontaireRepository;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        AssociationRepository $associationRepository,
        VolontaireRepository $volontaireRepository,
        Environment $twig,
        FlashBagInterface $session,
        MessageService $messageService,
        \Swift_Mailer $swiftMailer,
        RouterInterface $router,
        $to,
        $from
    ) {
        $this->twig = $twig;
        $this->session = $session;
        $this->messageService = $messageService;
        $this->swiftMailer = $swiftMailer;
        $this->to = $to;
        $this->from = $from;
        $this->associationRepository = $associationRepository;
        $this->volontaireRepository = $volontaireRepository;
        $this->router = $router;
    }

    public function send($from, $destinataires, $sujet, $body, $bcc = null)
    {
        $mail = (new \Swift_Message($sujet))
            ->setSubject($sujet)
            ->setFrom($from)
            ->setTo($destinataires);

        if ($bcc) {
            $mail->setBcc($bcc);
        }

        $mail->setBody($body);

        $this->swiftMailer->send($mail);
    }

    /**
     *
     * @param Message $data
     * @param $entities Volontaire[]|Association[]
     */
    public function sendMessage(Message $data, $entities, $from = null, UploadedFile $uploadedFile = null)
    {
        $attach = null;
        $sujet = $data->getSujet();
        $bodyOriginal = $data->getContenu();
        if (!$from) {
            $from = $this->from;
        }

        $message = (new \Swift_Message($sujet))
            ->setFrom($from)
            ->setSubject($sujet);

        if ($uploadedFile) {
            $attach = \Swift_Attachment::fromPath($uploadedFile)
                ->setFilename(
                    $uploadedFile->getClientOriginalName()
                )
                ->setContentType(
                    $uploadedFile->getClientMimeType()
                );
            $message->attach($attach);
        }

        foreach ($entities as $entity) {
            $user = $entity->getUser();
            $url = '';

            if ($user) {
                $token = $user->getToken();
                if ($token) {
                    $value = $token->getValue();
                    $url = $this->router->generate(
                        'volontariat_token_show',
                        ['value' => $value],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                }
            }
            $body = preg_replace("#{urltoken}#", $url, $bodyOriginal);

            $message->setBody($body);

            $destinataire = $this->messageService->getEmailEntity($entity);

            $message->setTo($destinataire);

            $this->swiftMailer->send($message);
        }
    }

    /**
     * Préviens les asbl
     * @param Volontaire $volontaire
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendNewVolontaire(Volontaire $volontaire)
    {
        $emails = $this->associationRepository->getAllEmail();

        $sujet = 'Un volontaire de plus sur la plate-forme du volontariat';
        $body = $this->twig->render(
            '@Volontariat/mail/volontaire_new.html.twig',
            array(
                "volontaire" => $volontaire,
            )
        );

        foreach ($emails as $email) {
            try {
                $this->send($this->from, $email, $sujet, $body);
            } catch (\Swift_SwiftException $e) {
                $this->session->add("error", $e->getMessage());
            }
        }
    }

    /**
     * L'admin doit valider une association
     * @param Association $association
     * @param User $user
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendAssociationToValider(Association $association, User $user)
    {
        $sujet = 'Une association a valider sur la plate-forme du volontariat';

        $body = $this->twig->render(
            '@Volontariat/mail/association_to_validate.html.twig',
            array(
                "association" => $association,
                "user" => $user,
            )
        );

        try {
            $this->send($this->from, $this->to, $sujet, $body);
        } catch (\Swift_SwiftException $e) {
            $this->session->add("error", $e->getMessage());
        }
    }

    /**
     * Prévient l'asbl qu'elle a été validée
     * @param Association $association
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendAssociationValidee(Association $association)
    {
        $sujet = 'Votre association a été validée sur la plate-forme du volontariat';

        $body = $this->twig->render(
            '@Volontariat/mail/association_validee.html.twig',
            array(
                "association" => $association,
            )
        );

        try {
            $this->send($this->from, $association->getEmail(), $sujet, $body);
        } catch (\Swift_SwiftException $e) {
            $this->session->add("error", $e->getMessage());
        }
    }

    /**
     * Les volontaires sont prévenus de l'arrivée d'une nouvelle Asbl
     * @param Association $association
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendNewAssociation(Association $association)
    {
        $sujet = 'Une nouvelle association sur la plate-forme du volontariat';
        $volontaires = $this->volontaireRepository->findBy(['valider' => true]);

        foreach ($volontaires as $volontaire) {
            $body = $this->twig->render(
                '@Volontariat/mail/association_new.html.twig',
                array(
                    "association" => $association,
                    "volontaire" => $volontaire,
                )
            );

            try {
                $this->send($this->from, $volontaire->getEmail(), $sujet, $body);
            } catch (\Swift_SwiftException $e) {
            }
        }
    }

    /**
     *
     * @param Message $data
     * @param Volontaire $volontaire
     * @param User $user
     */
    public function sendRecommanderVolontaire(Message $data, Volontaire $volontaire, User $user)
    {
        $sujet = $data->getSujet();
        $contenu = $data->getContenu();
        $nom = $data->getNom();
        $from = $data->getFroms();
        $destinataires = $data->getDestinataires();

        $mail = (new \Swift_Message($sujet))
            ->setSubject($sujet)
            ->setFrom($from)
            ->setTo($destinataires);

        $body = $this->twig->render(
            '@Volontariat/mail/_recommander_volontaire.html.twig',
            array(
                'volontaire' => $volontaire,
                'user' => $user,
                'contenu' => $contenu,
                'nom' => $nom,
            )
        );

        $mail->setBody($body);

        $this->swiftMailer->send($mail);
    }

    /**
     *
     * @param Message $data
     * @param Association $association
     * @param User $user
     */
    public function sendRecommanderAssociation(Message $data, Association $association, User $user)
    {
        $sujet = $data->getSujet();
        $contenu = $data->getContenu();
        $from = $data->getFroms();
        $nom = $data->getNom();
        $destinataires = $data->getDestinataires();

        $mail = (new \Swift_Message($sujet))
            ->setSubject($sujet)
            ->setFrom($from)
            ->setTo($destinataires);

        $body = $this->twig->render(
            '@Volontariat/mail/_recommander_association.html.twig',
            array(
                'association' => $association,
                'user' => $user,
                'contenu' => $contenu,
                'nom' => $nom,
            )
        );

        $mail->setBody($body);

        $this->swiftMailer->send($mail);
    }

    /**
     * @param Message $data
     * @param User $user
     */
    public function sendReferencer($data, $user)
    {
        $sujet = $data->getSujet();
        $contenu = $data->getContenu();
        $from = $data->getFroms();
        $nom = $data->getNom();
        $nomDestinataire = $data->getNomDestinataire();
        $destinataires = $data->getDestinataires();

        $mail = (new \Swift_Message($sujet))
            ->setSubject($sujet)
            ->setFrom($from)
            ->setTo($destinataires);

        $body = $this->twig->render(
            '@Volontariat/mail/_referencer.html.twig',
            array(
                'nomDestinataire' => $nomDestinataire,
                'user' => $user,
                'contenu' => $contenu,
                'nom' => $nom,
            )
        );

        $mail->setBody($body);

        $this->swiftMailer->send($mail);
    }
}
