<?php

namespace AcMarche\Volontariat\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait NotificationTrait
{
    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    public bool $notification_new_association = true;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    public bool $notification_message_association = true;
}
