<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/03/18
 * Time: 13:02
 */

namespace AcMarche\Volontariat\Validator\Constraints;

use AcMarche\Volontariat\Repository\CodePostalRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CodePostalIsBelgiumValidator extends ConstraintValidator
{
    public function __construct(private CodePostalRepository $codePostalRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if ($value) {
            if (!$this->codePostalRepository->findBy(['code' => (int)$value])) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
            }
        }
    }
}
