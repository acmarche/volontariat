<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 9/02/18
 * Time: 9:59
 */

namespace AcMarche\Volontariat\Service;

class VolontariatConstante
{
    public const ASSOCIATION_ADMIN_SEARCH = 'volontariat_admin_association_search';
    public const VOLONTAIRE_ADMIN_SEARCH = 'volontariat_admin_volontaire_search';
    public const ASSOCIATION_SEARCH = 'volontariat_association_search';
    public const VOLONTAIRE_SEARCH = 'volontariat_volontaire_search';

    public const VOLONTAIRE_NEW = 'volontariat.volontaire.new';
    public const VOLONTAIRE_EDIT = 'volontariat.volontaire.edit';
    public const VOLONTAIRE_DELETE = 'volontariat.volontaire.delete';

    public const ASSOCIATION_NEW = 'volontariat.association.new';
    public const ASSOCIATION_EDIT = 'volontariat.association.edit';
    public const ASSOCIATION_DELETE = 'volontariat.association.delete';
    public const ASSOCIATION_VALIDER_REQUEST = 'volontariat.association.valide.request';
    public const ASSOCIATION_VALIDER_FINISH = 'volontariat.association.validee.finish';

    public const ACTIVITE_NEW = 'volontariat.activite.new';
    public const ACTIVITE_EDIT = 'volontariat.activite.edit';
    public const ACTIVITE_DELETE = 'volontariat.activite.delete';
    public const ACTIVITE_VALIDER_REQUEST = 'volontariat.activite.validee.request';
    public const ACTIVITE_VALIDER_FINISH = 'volontariat.activite.validee.finish';
}
