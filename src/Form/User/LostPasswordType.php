<?php

namespace AcMarche\Volontariat\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

class LostPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('email', EmailType::class, [
                    'label' => 'Votre courriel',
                ]
            );
    }
}
