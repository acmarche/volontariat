<?php

namespace AcMarche\Volontariat\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NotificationTrait
{
    #[ORM\Column(type: 'boolean', nullable: false)]
    public bool $notification_new_association = true;

    #[ORM\Column(type: 'boolean', nullable: false)]
    public bool $notification_message_association = true;
}
