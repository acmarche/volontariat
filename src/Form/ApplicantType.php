<?php

namespace AcMarche\Volontariat\Form;

use AcMarche\Volontariat\Entity\Applicant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $villages = [
            'Aye',
            'Champlon',
            'Grimbièmont',
            'Hargimont',
            'Hollogne',
            'Humain',
            'Lignières',
            'On',
            'Mache-en-Famenne',
            'Marloie',
            'Roy',
            'Verdenne',
            'Waha'
        ];

        $builder
            ->add('name')
            ->add('surname')
            ->add(
                'city',
                ChoiceType::class,
                [
                    'choices' => array_combine($villages, $villages)
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => false
                ]
            )
            ->add('phone')
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Décrivez vos besoins',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            array(
                'data_class' => Applicant::class,
            )
        );
    }
}
