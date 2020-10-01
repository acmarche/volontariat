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
    const ACTIVITE_NEW = VolontariatConstante::ACTIVITE_NEW;
    const ACTIVITE_EDIT = VolontariatConstante::ACTIVITE_EDIT;
    const ACTIVITE_DELETE = VolontariatConstante::ACTIVITE_DELETE;
    const ACTIVITE_VALIDER_REQUEST = VolontariatConstante::ACTIVITE_VALIDER_REQUEST;
    const ACTIVITE_VALIDER_FINISH = VolontariatConstante::ACTIVITE_VALIDER_FINISH;

    protected $activite;

    public function __construct(Activite $activite)
    {
        $this->activite = $activite;
    }

    public function getActivite()
    {
        return $this->activite;
    }
}
