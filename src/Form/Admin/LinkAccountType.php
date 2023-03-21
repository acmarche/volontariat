<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LinkAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'user',
                EntityType::class,
                [
                    'class' => User::class,
                    'query_builder' => fn (UserRepository $userRepository) => $userRepository->qbqForList(),
                    'required' => false,
                    'choice_label' => fn ($user) => $user->name.' '.$user->surname.' ('.$user->email.')',
                    'placeholder' => 'Sélectionnez un utilisateur',
                ]
            );
    }
}
