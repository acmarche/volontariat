<?php

namespace AcMarche\Volontariat\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmptyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {

    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            array()
        );
    }
}
