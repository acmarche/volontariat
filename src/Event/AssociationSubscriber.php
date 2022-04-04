<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 13/09/17
 * Time: 17:00
 */

namespace AcMarche\Volontariat\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use AcMarche\Volontariat\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AssociationSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em, private Mailer $mailer, private TokenStorageInterface $token)
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
            AssociationEvent::ASSOCIATION_VALIDER_REQUEST => 'associationRequest',
            AssociationEvent::ASSOCIATION_VALIDER_FINISH => 'associationValideeFinish',
            AssociationEvent::ASSOCIATION_NEW => 'associationNew',
        ];
    }

    /**
     * Mail indiquant a l'admin qu'il doit valider
     */
    public function associationRequest(AssociationEvent $event): void
    {
        $association = $event->getAssociation();
        $user = $this->getCurrentUser();
        $this->mailer->sendAssociationToValider($association, $user);
    }

    /**
     * Previent l'asbl qu'elle a été validée
     */
    public function associationValideeFinish(AssociationEvent $event): void
    {
        $association = $event->getAssociation();

        $this->mailer->sendAssociationValidee($association);
    }


    /**
     * Mail indiquant aux volontaires qu'une asbl a été ajoutée
     */
    public function associationNew(AssociationEvent $event): void
    {
        $association = $event->getAssociation();
        $this->mailer->sendNewAssociation($association);
    }

    protected function getCurrentUser(): ?UserInterface
    {
        $token = $this->token->getToken();
        return $user = $token->getUser();
    }
}
