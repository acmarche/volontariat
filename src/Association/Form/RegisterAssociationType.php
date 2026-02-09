<?php

namespace AcMarche\Volontariat\Association\Form;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Secteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterAssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('name', TextType::class, [
                'label' => "Nom de l'association",
            ])
            ->add('address', TextType::class, [
                'label' => 'Rue',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Numéro de téléphone',
            ])
            ->add(
                'postalCode',
                TextType::class,
                [
                    'label' => 'Code postal',
                ]
            )
            ->add(
                'city',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'Localité',
                    'attr' => ['autocomplete' => 'city'],
                )
            )
            ->add('email', EmailType::class, [
                'label' => 'Courriel',
                'help' => 'Cette adresse mail servira à la gestion de la fiche de l’association. Il est conseillé d\'utiliser une adresse e-mail de l’association, et non à une adresse personnelle.',
            ])
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => "Description de l'association",
                    'attr' => [
                        'rows' => 8,
                    ],
                ]
            )
            ->add(
                'secteurs',
                EntityType::class,
                [
                    'class' => Secteur::class,
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            array(
                'data_class' => Association::class,
            )
        );
    }
}
