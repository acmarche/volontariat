<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Manager\LinkManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssocierAssociationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'users',
                EntityType::class,
                array(
                    'class' => User::class,
                    'required' => true,
                    'placeholder' => "Sélectionnez un utilisateur",
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => LinkManager::class,
            )
        );
    }
}
