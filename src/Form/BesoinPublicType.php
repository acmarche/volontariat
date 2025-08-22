<?php

namespace AcMarche\Volontariat\Form;

use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Form\Admin\BesoinType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BesoinPublicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(array(
            'data_class' => Besoin::class
        ));
    }

    public function getParent(): ?string
    {
        return BesoinType::class;
    }
}
