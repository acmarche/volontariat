<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/02/18
 * Time: 10:53
 */

namespace AcMarche\Volontariat\Event;

use AcMarche\Volontariat\Entity\Volontaire;
use Symfony\Contracts\EventDispatcher\Event;

class VolontaireEvent extends Event
{
    public const VOLONTAIRE_NEW = VolontariatEnum::VOLONTAIRE_NEW;

    public function __construct(protected Volontaire $volontaire)
    {
    }

    public function getVolontaire(): Volontaire
    {
        return $this->volontaire;
    }
}
