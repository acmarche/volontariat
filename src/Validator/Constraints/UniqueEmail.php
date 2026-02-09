<?php

namespace AcMarche\Volontariat\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class UniqueEmail extends Constraint
{
    public string $message = 'L\'adresse email "{{ email }}" est déjà utilisée.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
