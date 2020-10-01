<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/02/18
 * Time: 10:53
 */

namespace AcMarche\Volontariat\Event;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Service\VolontariatConstante;
use Symfony\Contracts\EventDispatcher\Event;

class AssociationEvent extends Event
{
    const ASSOCIATION_NEW = VolontariatConstante::ASSOCIATION_NEW;
    const ASSOCIATION_EDIT = VolontariatConstante::ASSOCIATION_EDIT;
    const ASSOCIATION_DELETE = VolontariatConstante::ASSOCIATION_DELETE;
    const ASSOCIATION_VALIDER_REQUEST = VolontariatConstante::ASSOCIATION_VALIDER_REQUEST;
    const ASSOCIATION_VALIDER_FINISH = VolontariatConstante::ASSOCIATION_VALIDER_FINISH;

    protected $association;

    public function __construct(Association $association)
    {
        $this->association = $association;
    }

    public function getAssociation()
    {
        return $this->association;
    }
}
