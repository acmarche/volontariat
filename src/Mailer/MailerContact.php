<?php

namespace AcMarche\Volontariat\Mailer;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerContact
{
    use MailerTrait;

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly VolontaireRepository $volontaireRepository,
        #[Autowire('%env(VOLONTARIAT_MAILER_FROM)%')]
        private readonly string $from
    ) {
    }

    /**
     * Envoie formulaire page contact.
     *
     * @throws TransportExceptionInterface
     */
    public function sendContact(array $data): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($this->from,$data['email']))
            ->to(new Address($this->from))
            ->subject('Contact sur la plate-forme du volontariat')
            ->htmlTemplate('@Volontariat/emails/_page_contact.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $data,
                ])
            );

        $this->mailer->send($templatedEmail);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendToVolontaire(Volontaire $volontaire, array $data): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($this->from,$data['email']))
            ->to(new Address($volontaire->email))
            ->bcc(new Address($this->from))
            ->subject('On vous contact via la plate-forme du volontariat')
            ->htmlTemplate('@Volontariat/emails/_to_volontaire.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $data,
                    'volontaire' => $volontaire,
                ])
            );

        $this->mailer->send($templatedEmail);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendToAssociation(Association $association, array $data): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($this->from,$data['email']))
            ->to(new Address($this->from))
            ->bcc(new Address($this->from))
            ->subject('On vous contact via la plate-forme du volontariat')
            ->htmlTemplate('@Volontariat/emails/_to_association.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $data,
                    'association' => $association,
                ])
            );

        $this->mailer->send($templatedEmail);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendReferencerVolontaire(Association $association, Volontaire $volontaire, Message $message): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($this->from,$message['email']))
            ->to(new Address($volontaire->email))
            ->bcc(new Address($this->from))
            ->subject($association->name.' vous recommande un volontaire')
            ->htmlTemplate('@Volontariat/emails/_recommande_volontaire.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $message,
                    'association' => $association,
                    'volontaire' => $volontaire,
                ])
            );

        $this->mailer->send($templatedEmail);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendReferencerAssociation(Association $association, Message $message): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($this->from))
            ->to(new Address($message->to))
            ->bcc(new Address($this->from))
            ->subject($message->sujet.' vous recommande une association')
            ->htmlTemplate('@Volontariat/emails/_recommande_association.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $message,
                    'association' => $association,
                ])
            );

        $this->mailer->send($templatedEmail);
    }

    public function sendToVolontairesBySecteur(Secteur $secteur, mixed $data)
    {
        $volontaires = $this->volontaireRepository->findVolontaireBySecteur($secteur);
        dd($volontaires);

        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($this->from))
            ->to(new Address($message->to))
            ->bcc(new Address($this->from))
            ->subject($message->sujet.' vous recommande une association')
            ->htmlTemplate('@Volontariat/emails/_recommande_association.html.twig')
            ->context(
                array_merge($this->defaultParams(), [
                    'data' => $message,
                    'association' => $association,
                ])
            );

        $this->mailer->send($templatedEmail);
    }
}
