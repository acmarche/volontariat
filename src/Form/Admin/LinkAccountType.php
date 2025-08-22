<?php

namespace AcMarche\Volontariat\Form\Admin;

use Doctrine\ORM\QueryBuilder;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LinkAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'user',
                EntityType::class,
                [
                    'class' => User::class,
                    'query_builder' => fn (UserRepository $userRepository): QueryBuilder => $userRepository->qbqForList(),
                    'required' => false,
                    'choice_label' => fn ($user): string => $user->name.' '.$user->surname.' ('.$user->email.')',
                    'placeholder' => 'Sélectionnez un utilisateur',
                ]
            );
    }
}
