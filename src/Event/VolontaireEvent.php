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
    const VOLONTAIRE_NEW = VolontariatConstante::VOLONTAIRE_NEW;
    const VOLONTAIRE_EDIT = VolontariatConstante::VOLONTAIRE_EDIT;
    const VOLONTAIRE_DELETE = VolontariatConstante::VOLONTAIRE_DELETE;

    protected $volontaire;

    public function __construct(Volontaire $volontaire)
    {
        $this->volontaire = $volontaire;
    }

    public function getVolontaire()
    {
        return $this->volontaire;
    }
}
