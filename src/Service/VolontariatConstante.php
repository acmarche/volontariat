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
    const ASSOCIATION_ADMIN_SEARCH = 'volontariat_admin_association_search';
    const VOLONTAIRE_ADMIN_SEARCH = 'volontariat_admin_volontaire_search';
    const ASSOCIATION_SEARCH = 'volontariat_association_search';
    const VOLONTAIRE_SEARCH = 'volontariat_volontaire_search';

    const VOLONTAIRE_NEW = 'volontariat.volontaire.new';
    const VOLONTAIRE_EDIT = 'volontariat.volontaire.edit';
    const VOLONTAIRE_DELETE = 'volontariat.volontaire.delete';

    const ASSOCIATION_NEW = 'volontariat.association.new';
    const ASSOCIATION_EDIT = 'volontariat.association.edit';
    const ASSOCIATION_DELETE = 'volontariat.association.delete';
    const ASSOCIATION_VALIDER_REQUEST = 'volontariat.association.valide.request';
    const ASSOCIATION_VALIDER_FINISH = 'volontariat.association.validee.finish';

    const ACTIVITE_NEW = 'volontariat.activite.new';
    const ACTIVITE_EDIT = 'volontariat.activite.edit';
    const ACTIVITE_DELETE = 'volontariat.activite.delete';
    const ACTIVITE_VALIDER_REQUEST = 'volontariat.activite.validee.request';
    const ACTIVITE_VALIDER_FINISH = 'volontariat.activite.validee.finish';
}
