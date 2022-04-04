<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Manager\LinkManager;
use AcMarche\Volontariat\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssocierVolontaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => LinkManager::class,
            ]
        );
    }
}
