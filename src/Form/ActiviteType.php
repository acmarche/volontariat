<?php

namespace AcMarche\Volontariat\Form;

use AcMarche\Volontariat\Entity\Activite;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'titre',
                TextType::class,
                [
                    'attr' => ['size' => 60],
                ]
            )
            ->add(
                'content',
                CKEditorType::class,
                [
                    'config_name' => 'volontariat_config',
                    'attr' => [
                        'rows' => 40,
                        'cols' => 80,
                    ],
                ]
            )
            ->add(
                'lieu',
                TextType::class,
                [
                    'attr' => ['size' => 60],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            array(
                'data_class' => Activite::class,
            )
        );
    }
}
