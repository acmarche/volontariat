<?php

namespace AcMarche\Volontariat\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Length;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'plainPassword',
                TextType::class,
                [
                    'label' => 'Nouveau mot de passe',
                    'help' => 'Minimum 8 caractères',
                    'attr' => ['autocomplete' => false],
                    'constraints' => [new Length(min: 8, max: 30)],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => UserInterface::class,
            ]
        );
    }
}
