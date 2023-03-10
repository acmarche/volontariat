<?php

namespace AcMarche\Volontariat\Voluntary\Form;

use AcMarche\Volontariat\Entity\Volontaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterVoluntaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'name',
                TextType::class,
                array(
                    'required' => true,
                    'attr' => ['autocomplete' => 'family-name'],
                )
            )
            ->add(
                'surname',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'Prénom',
                )
            )
            ->add(
                'city',
                TextType::class,
                array(
                    'required' => true,
                    'attr' => ['autocomplete' => 'city'],
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            array(
                'data_class' => Volontaire::class,
            )
        );
    }
}
