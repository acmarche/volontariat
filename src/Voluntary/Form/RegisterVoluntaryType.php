<?php

namespace AcMarche\Volontariat\Voluntary\Form;

use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Entity\Volontaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterVoluntaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                    'attr' => ['autocomplete' => 'family-name'],
                ]
            )
            ->add(
                'surname',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Prénom',
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Localité',
                    'attr' => ['autocomplete' => 'city'],
                ]
            )
            ->add('accord', CheckboxType::class, [
                'required' => true,
                'label' => false,
            ])
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
            [
                'data_class' => Volontaire::class,
            ]
        );
    }
}
