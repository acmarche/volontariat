<?php

namespace AcMarche\Volontariat\Form;

use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Admin\VolontaireType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VolontairePublicType extends AbstractType
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
            'data_class' => Volontaire::class
        ));
    }

    public function getParent()
    {
        return VolontaireType::class ;
    }
}
