<?php

namespace AcMarche\Volontariat\Form\User;

use AcMarche\Volontariat\Entity\Security\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                array(
                    'required' => true,
                    'attr'=>['placeholder'=>'Email']
                )
            )
            ->add(
                'nom',
                TextType::class,
                array(
                    'required' => true,
                    'attr'=>['placeholder'=>'Nom']
                )
            )
            ->add(
                'prenom',
                TextType::class,
                array(
                    'required' => true,
                    'attr'=>['placeholder'=>'Prénom']
                )
            )
            ->add(
                'accord',
                CheckboxType::class,
                [
                    'label' => "J'ai pris connaissance du réglement de la vie privée",
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
            )
        );
    }
}
