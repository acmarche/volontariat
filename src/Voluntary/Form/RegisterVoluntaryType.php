<?php

namespace AcMarche\Volontariat\Voluntary\Form;

use AcMarche\Volontariat\Entity\Security\User;
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
                'help_html' => true,
                'help' => '<a href="https://www.marche.be/administration/rgpd/" target="_blank">J\'ai pris connaissance du règlement de la vie privée</a>',
            ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
