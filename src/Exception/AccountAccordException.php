<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 14/11/18
 * Time: 11:41
 */

namespace AcMarche\Volontariat\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AccountAccordException extends AccountStatusException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Pas de accord.';
    }
}