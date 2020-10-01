<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Temoignage;
use AcMarche\Volontariat\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemoignageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                array('help'=>'Votre prénom ou nom d\'association')
            )
            ->add('village', TextType::class, array(
            ))
            ->add(
                'message',
                TextareaType::class,
                array()
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(Temoignage::class));
    }
}
