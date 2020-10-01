<?php

namespace AcMarche\Volontariat\Form;

use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Form\Admin\BesoinType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BesoinPublicType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Besoin::class
        ));
    }

    public function getParent()
    {
        return BesoinType::class;
    }
}
