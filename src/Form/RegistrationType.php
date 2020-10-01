<?php

namespace AcMarche\Volontariat\Form;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                array(
                    'required' => true,
                    'attr' => ['placeholder' => 'Votre nom'],
                )
            )
            ->add(
                'prenom',
                TextType::class,
                array(
                    'required' => true,
                    'attr' => ['placeholder' => 'Votre prénom'],
                )
            )
            ->add(
                'accord',
                CheckboxType::class,
                [
                    'label' => "Je donne mon consentement",
                ]
            );
        $builder->remove('username');
    }

    public function getParent()
    {
        return RegistrationFormType::class;
    }
}
