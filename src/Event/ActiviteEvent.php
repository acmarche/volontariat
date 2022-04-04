<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/02/18
 * Time: 10:53
 */

namespace AcMarche\Volontariat\Event;

use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Service\VolontariatConstante;
use Symfony\Contracts\EventDispatcher\Event;

class ActiviteEvent extends Event
{
    public const ACTIVITE_NEW = VolontariatConstante::ACTIVITE_NEW;
    public const ACTIVITE_EDIT = VolontariatConstante::ACTIVITE_EDIT;
    public const ACTIVITE_DELETE = VolontariatConstante::ACTIVITE_DELETE;
    public const ACTIVITE_VALIDER_REQUEST = VolontariatConstante::ACTIVITE_VALIDER_REQUEST;
    public const ACTIVITE_VALIDER_FINISH = VolontariatConstante::ACTIVITE_VALIDER_FINISH;

    public function __construct(protected Activite $activite)
    {
    }

    public function getActivite(): Activite
    {
        return $this->activite;
    }
}
