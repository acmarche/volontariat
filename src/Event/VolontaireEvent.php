<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/02/18
 * Time: 10:53
 */

namespace AcMarche\Volontariat\Event;

use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Service\VolontariatConstante;
use Symfony\Contracts\EventDispatcher\Event;

class VolontaireEvent extends Event
{
    public const VOLONTAIRE_NEW = VolontariatConstante::VOLONTAIRE_NEW;
    public const VOLONTAIRE_EDIT = VolontariatConstante::VOLONTAIRE_EDIT;
    public const VOLONTAIRE_DELETE = VolontariatConstante::VOLONTAIRE_DELETE;

    public function __construct(protected Volontaire $volontaire)
    {
    }

    public function getVolontaire(): Volontaire
    {
        return $this->volontaire;
    }
}
