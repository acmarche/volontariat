<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Besoin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BesoinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Titre',
                ]
            )
            ->add(
                'date_begin',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'required' => true,
                    'help' => 'Date de début de diffusion, format 20/10/2019',
                    'attr' => array(
                        'class' => 'datepicker',
                    ),
                )
            )
            ->add(
                'date_end',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'required' => true,
                    'help' => 'Date de fin de diffusion, format 20/10/2019',
                )
            )
            ->add('requirement')
            ->add('period')
            ->add('place');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            array(
                'data_class' => Besoin::class,
            )
        );
    }
}
