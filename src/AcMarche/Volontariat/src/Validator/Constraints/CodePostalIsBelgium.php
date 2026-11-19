<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/03/18
 * Time: 12:59
 */

namespace AcMarche\Volontariat\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CodePostalIsBelgium extends Constraint
{
    public $message = 'Le code postal "{{ string }}" doit être un code postal belge.';
}
