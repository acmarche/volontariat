<?php

namespace AcMarche\Volontariat\Mailer;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Mailer
{
    use MailerTrait;

    public function __construct(
        private MessageService $messageService,
        private MailerInterface $mailer,
        private string $from
    ) {
    }

    /**
     * @param array $data
     *
     * @throws TransportExceptionInterface
     */
    public function sendRegularMessage(
        string $to,
        Message $data,
        ?string $url = null
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
                ])
            );

        if ($uploadedFile = $data->file) {
            if ($uploadedFile instanceof UploadedFile) {
                $email->attachFromPath(
                    $uploadedFile->getPathname(),
                    $uploadedFile->getClientOriginalName(),
                    $uploadedFile->getClientMimeType()
                );
            }
        }
        $this->mailer->send($email);
    }

    /**
     * @param array $data
     *
     * @throws TransportExceptionInterface
     */
    public function sendAutoAssociation(
        Association $association,
        string $to,
        string $urlAccount,
        string $urlAssociations,
        string $urlVolontaires
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
                ])
            );


        $this->mailer->send($email);
    }

}
