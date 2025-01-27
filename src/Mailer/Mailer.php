<?php

namespace AcMarche\Volontariat\Mailer;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Entity\Volontaire;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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
        private string $from,
    ) {}

    /**
     * @param array $data
     *
     * @throws TransportExceptionInterface
     */
    public function sendRegularMessage(
        string $to,
        Message $data,
        ?string $url = null,
    ): void {
        $content = $data->contenu;

        $email = (new TemplatedEmail())
            ->from($this->from)
            ->to(new Address($to))
            ->subject($data->sujet)
            ->htmlTemplate('@Volontariat/emails/_to_all.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $data,
                    'content' => $content,
                    'url' => $url,
                ]),
            );

        if ($uploadedFile = $data->file) {
            if ($uploadedFile instanceof UploadedFile) {
                $email->attachFromPath(
                    $uploadedFile->getPathname(),
                    $uploadedFile->getClientOriginalName(),
                    $uploadedFile->getClientMimeType(),
                );
            }
        }
        $this->mailer->send($email);
    }

    /**
     * @param Association $association
     * @param string $to
     * @param string $urlAccount
     * @param string $urlAssociations
     * @param string $urlVolontaires
     * @throws TransportExceptionInterface
     */
    public function sendAutoAssociation(
        Association $association,
        string $to,
        string $urlAccount,
        string $urlAssociations,
        string $urlVolontaires,
    ): void {
        $email = (new TemplatedEmail())
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


        $this->mailer->send($email);
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
        $email = (new TemplatedEmail())
            ->from($association->email)
            ->to(new Address($volontaire->email))
            ->bcc(new Address('jf@marche.be'))
            ->subject('Un nouveau besoin sur la plate-forme du volontariat')
            ->htmlTemplate('@Volontariat/emails/_new_besoin.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'besoin' => $besoin,
                    'association' => $besoin->getAssociation(),
                    'url' => $urlLink,
                ]),
            );

        $this->mailer->send($email);
    }

}
