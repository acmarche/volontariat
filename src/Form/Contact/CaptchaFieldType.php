<?php

namespace AcMarche\Volontariat\Form\Contact;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CaptchaFieldType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'required' => true,
                'attr' => ['id' => 'contact_captcha'],
            ]
        );
    }

    public function getParent(): ?string
    {
        return HiddenType::class;
    }
}
