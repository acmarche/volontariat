<?php

namespace AcMarche\Volontariat\Validator\Constraints;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Security\EmailUniquenessChecker;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEmailValidator extends ConstraintValidator
{
    public function __construct(private EmailUniquenessChecker $emailUniquenessChecker)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new UnexpectedTypeException($constraint, UniqueEmail::class);
        }

        if (!$value instanceof Association && !$value instanceof Volontaire) {
            return;
        }

        $email = $value->email;

        if (!$email) {
            return;
        }

        if (!$this->emailUniquenessChecker->isEmailAvailable($email, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ email }}', $email)
                ->atPath('email')
                ->addViolation();
        }
    }
}
