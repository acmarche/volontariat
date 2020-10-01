<?php

namespace AcMarche\Volontariat\Form;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Form\Admin\AssocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociationPublicType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('valider');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Association::class
        ));
    }

    public function getParent()
    {
        return AssocationType::class ;
    }
}
