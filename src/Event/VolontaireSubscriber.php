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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class VolontaireSubscriber implements EventSubscriberInterface
{
    private $em;
    private $mailer;
    private $token;
    private $session;

    public function __construct(
        EntityManagerInterface $em,
        Mailer $mailer,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack
    ) {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->token = $tokenStorage;
        $this->session = $requestStack->getSession();
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
            VolontaireEvent::VOLONTAIRE_NEW => 'volontaireNew',
        ];
    }

    /**
     * Mail envoyé aux associations
     * @param VolontaireEvent $event
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function volontaireNew(VolontaireEvent $event)
    {
        $volontaire = $event->getVolontaire();
        $user = $volontaire->getUser();

        $to = [];
        $to[] = "adl@marche.be";
        $to[] = "jf@marche.be";

        $this->mailer->sendNewVolontaire($volontaire);
    }

    protected function getCurrentUser()
    {
        $token = $this->token->getToken($this->session->getId());

        return $user = $token->getUser();
    }
}
