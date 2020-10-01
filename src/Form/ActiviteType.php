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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Activite::class,
            )
        );
    }
}
