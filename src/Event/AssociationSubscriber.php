<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 13/09/17
 * Time: 17:00
 */

namespace AcMarche\Volontariat\Event;

use AcMarche\Volontariat\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AssociationSubscriber implements EventSubscriberInterface
{
    private $em;
    private $mailer;
    private $token;

    public function __construct(
        EntityManagerInterface $em,
        Mailer $mailer,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $em;
        $this->mailer = $mailer;
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
            AssociationEvent::ASSOCIATION_VALIDER_REQUEST => 'associationRequest',
            AssociationEvent::ASSOCIATION_VALIDER_FINISH => 'associationValideeFinish',
            AssociationEvent::ASSOCIATION_NEW => 'associationNew',
        ];
    }

    /**
     * Mail indiquant a l'admin qu'il doit valider
     * @param AssociationEvent $event
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function associationRequest(AssociationEvent $event)
    {
        $association = $event->getAssociation();
        $user = $this->getCurrentUser();
        $this->mailer->sendAssociationToValider($association, $user);
    }

    /**
     * Previent l'asbl qu'elle a été validée
     * @param AssociationEvent $event
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function associationValideeFinish(AssociationEvent $event)
    {
        $association = $event->getAssociation();

        $this->mailer->sendAssociationValidee($association);
    }


    /**
     * Mail indiquant aux volontaires qu'une asbl a été ajoutée
     * @param AssociationEvent $event
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function associationNew(AssociationEvent $event)
    {
        $association = $event->getAssociation();
        $this->mailer->sendNewAssociation($association);
    }

    protected function getCurrentUser()
    {
        $token = $this->token->getToken();
        return $user = $token->getUser();
    }
}
