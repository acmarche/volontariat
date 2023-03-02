<?php

/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 13/09/17
 * Time: 17:00
 */

namespace AcMarche\Volontariat\Event;

use AcMarche\Volontariat\Mailer\MailerActivite;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ActiviteSubscriber implements EventSubscriberInterface
{
    public function __construct(private MailerActivite $mailerActivite, private TokenStorageInterface $token)
    {
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ActiviteEvent::ACTIVITE_VALIDER_REQUEST->value => 'activiteRequest',
            ActiviteEvent::ACTIVITE_VALIDER_FINISH->value => 'activiteValidee',
            ActiviteEvent::ACTIVITE_NEW->value => 'activiteNew',
        ];
    }

    /**
     * Mail indiquant a l'admin qu'il doit valider
     */
    public function activiteRequest(ActiviteEvent $event): void
    {
        $activite = $event->getActivite();
        $user = $this->getCurrentUser();
        $this->mailerActivite->sendRequest($activite, $user);
    }

    /**
     * Previent l'asbl qu'elle a été validée
     */
    public function activiteValidee(ActiviteEvent $event): void
    {
        $activite = $event->getActivite();
        $this->mailerActivite->sendFinish($activite);
    }

    /**
     * Mail indiquant aux volontaires qu'une asbl a été ajoutée
     */
    public function activiteNew(ActiviteEvent $event): void
    {
        $activite = $event->getActivite();
        $this->mailerActivite->sendNew($activite);
    }

    protected function getCurrentUser(): ?UserInterface
    {
        $token = $this->token->getToken();

        return $user = $token->getUser();
    }
}
