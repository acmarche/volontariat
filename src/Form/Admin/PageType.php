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
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'title',
                TextType::class,
                [
                    'attr' => ['size' => 60],
                ]
            )
            ->add(
                'excerpt',
                TextType::class,
                [
                    'label' => 'Introduction',
                    'help' => "Pour la page d'accueil",
                    'attr' => ['size' => 60],
                ]
            )
            ->add(
                'actualite',
                CheckboxType::class,
                [
                    'required' => false,
                    'help' => 'Pour afficher en page d\'accueil dans la partie actualités',
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

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Page::class,
            ]
        );
    }
}
