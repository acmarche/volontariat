<?php

namespace AcMarche\Volontariat\Form\User;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Voluntary\Form\RegisterVoluntaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditPublicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('username')
            ->remove('city')
            ->remove('accord')
            ->remove('plainPassword');
    }

    public function getParent(): ?string
    {
        return RegisterVoluntaryType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}