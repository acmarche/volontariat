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
    public const ASSOCIATION_NEW = VolontariatConstante::ASSOCIATION_NEW;
    public const ASSOCIATION_EDIT = VolontariatConstante::ASSOCIATION_EDIT;
    public const ASSOCIATION_DELETE = VolontariatConstante::ASSOCIATION_DELETE;
    public const ASSOCIATION_VALIDER_REQUEST = VolontariatConstante::ASSOCIATION_VALIDER_REQUEST;
    public const ASSOCIATION_VALIDER_FINISH = VolontariatConstante::ASSOCIATION_VALIDER_FINISH;

    public function __construct(protected Association $association)
    {
    }

    public function getAssociation(): Association
    {
        return $this->association;
    }
}
