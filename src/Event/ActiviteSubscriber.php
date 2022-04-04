<?php

/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 13/09/17
 * Time: 17:00
 */

namespace AcMarche\Volontariat\Event;

use AcMarche\Volontariat\Service\MailerActivite;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActiviteSubscriber implements EventSubscriberInterface
{
    private $mailerActivite;
    private $token;

    public function __construct(
        MailerActivite $mailer,
        TokenStorageInterface $tokenStorage
    ) {
        $this->mailerActivite = $mailer;
        $this->token = $tokenStorage;
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
    public static function getSubscribedEvents()
    {
        return [
            ActiviteEvent::ACTIVITE_VALIDER_REQUEST => 'activiteRequest',
            ActiviteEvent::ACTIVITE_VALIDER_FINISH => 'activiteValidee',
            ActiviteEvent::ACTIVITE_NEW => 'activiteNew',
        ];
    }

    /**
     * Mail indiquant a l'admin qu'il doit valider
     * @param ActiviteEvent $event

     */
    public function activiteRequest(ActiviteEvent $event)
    {
        $activite = $event->getActivite();
        $user = $this->getCurrentUser();
        $this->mailerActivite->sendRequest($activite, $user);
    }

    /**
     * Previent l'asbl qu'elle a été validée
     * @param ActiviteEvent $event

     */
    public function activiteValidee(ActiviteEvent $event)
    {
        $activite = $event->getActivite();
        $this->mailerActivite->sendFinish($activite);
    }

    /**
     * Mail indiquant aux volontaires qu'une asbl a été ajoutée
     * @param ActiviteEvent $event

     */
    public function activiteNew(ActiviteEvent $event)
    {
        $activite = $event->getActivite();
        $this->mailerActivite->sendNew($activite);
    }

    protected function getCurrentUser()
    {
        $token = $this->token->getToken();

        return $user = $token->getUser();
    }
}
