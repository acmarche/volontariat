<?php

namespace AcMarche\Volontariat\Security;

enum RolesEnum: string
{
    case volontaire = 'ROLE_VOLONTARIAT';
    case association = 'ROLE_ASSOCIATION';
    case admin = 'ROLE_VOLONTARIAT_ADMIN';
}
