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
    /**
     * @var CodePostalRepository
     */
    private $codePostalRepository;

    public function __construct(CodePostalRepository $codePostalRepository)
    {
        $this->codePostalRepository = $codePostalRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$this->codePostalRepository->findBy(['code' => intval($value)])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
