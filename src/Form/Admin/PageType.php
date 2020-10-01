<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Page;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'attr' => ['size' => 60],
                ]
            )
            ->add(
                'actualite',
                CheckboxType::class,
                [
                    'required'=>false,
                    'help'=>'Pour afficher dans la partie actualités',
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
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Page::class,
            )
        );
    }
}
