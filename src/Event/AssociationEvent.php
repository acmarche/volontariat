<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/02/18
 * Time: 10:53
 */

namespace AcMarche\Volontariat\Event;

use AcMarche\Volontariat\Entity\Association;
use Symfony\Contracts\EventDispatcher\Event;

class AssociationEvent extends Event
{
    public const ASSOCIATION_NEW = VolontariatEnum::ASSOCIATION_NEW;
    public const ASSOCIATION_VALIDER_REQUEST = VolontariatEnum::ASSOCIATION_VALIDER_REQUEST;
    public const ASSOCIATION_VALIDER_FINISH = VolontariatEnum::ASSOCIATION_VALIDER_FINISH;

    public function __construct(protected Association $association)
    {
    }

    public function getAssociation(): Association
    {
        return $this->association;
    }
}
