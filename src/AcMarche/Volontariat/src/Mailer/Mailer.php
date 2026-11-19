<?php

namespace AcMarche\Volontariat\Mailer;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Entity\Volontaire;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    use MailerTrait;

    public function __construct(
        private MessageService $messageService,
        private MailerInterface $mailer,
        #[Autowire('%env(VOLONTARIAT_MAILER_FROM)%')]
        private string $from,
    ) {
    }

    /**
     * Envoie depuis l'admin vers associations ou volontaires
     * @param string $to
     * @param Message $message
     * @param string|null $url
     * @throws TransportExceptionInterface
     */
    public function sendRegularMessage(
        string $to,
        Message $message,
        ?string $url = null,
    ): void {
        $content = $message->contenu;

        $templatedEmail = (new TemplatedEmail())
            ->from($this->from)
            ->to(new Address($to))
            ->subject($message->sujet)
            ->htmlTemplate('@Volontariat/emails/_to_all.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $message,
                    'content' => $content,
                    'url' => $url,
                ]),
            );

        if (($uploadedFile = $message->file) instanceof File && $uploadedFile instanceof UploadedFile) {
            $templatedEmail->attachFromPath(
                $uploadedFile->getPathname(),
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getClientMimeType(),
            );
        }

        $this->mailer->send($templatedEmail);
    }

    /**
     * Depuis une commande, non utilisé
     * @throws TransportExceptionInterface
     */
    public function sendAutoAssociation(
        Association $association,
        string $to,
        string $urlAccount,
        string $urlAssociations,
        string $urlVolontaires,
    ): void {
        $templatedEmail = (new TemplatedEmail())
            ->from($this->from)
            ->to(new Address($to))
            ->subject('Plate-forme du Volontariat')
            ->htmlTemplate('@Volontariat/emails/_auto_assoc.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'association' => $association,
                    'urlAccount' => $urlAccount,
                    'urlAssociations' => $urlAssociations,
                    'urlVolontaires' => $urlVolontaires,
                ]),
            );


        $this->mailer->send($templatedEmail);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendNewBesoin(
        Besoin $besoin,
        Association $association,
        Volontaire $volontaire,
        ?string $urlLink,
    ): void {
        $templatedEmail = (new TemplatedEmail())
            ->from($association->email)
            ->to(new Address($volontaire->email))
            ->replyTo(new Address($association->email))
            ->bcc(new Address('jf@marche.be'))
            ->subject('Une nouvelle annonce sur la plate-forme du volontariat')
            ->htmlTemplate('@Volontariat/emails/_new_besoin.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'besoin' => $besoin,
                    'association' => $association,
                    'url' => $urlLink,
                ]),
            );

        $this->mailer->send($templatedEmail);
    }

}
