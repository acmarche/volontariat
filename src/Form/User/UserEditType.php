<?php

namespace AcMarche\Volontariat\Form\User;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Security\SecurityData;
use AcMarche\Volontariat\Voluntary\Form\RegisterVoluntaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserEditType extends AbstractType
{
    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('username')
            ->remove('plainPassword');

        if ($this->authorizationChecker->isGranted(SecurityData::getRoleAdmin())) {
            $builder->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => SecurityData::getRoles(),
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
        }
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
