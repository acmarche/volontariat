<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 7/11/18
 * Time: 9:00
 */

namespace AcMarche\Volontariat\Security;

class SecurityData
{
    public static function getRoles(): iterable
    {
        $roles = [self::getRoleAdmin(), self::getRoleVolontariat(), self::getRoleAssociation()];

        return array_combine($roles, $roles);
    }

    public static function getRoleAdmin(): string
    {
        return RolesEnum::admin->value;
    }

    public static function getRoleVolontariat(): string
    {
        return RolesEnum::volontaire->value;
    }

    public static function getRoleAssociation(): string
    {
        return RolesEnum::association->value;
    }
}
