<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Secteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AssocationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('address')
            ->add('number')
            ->add(
                'postalCode',
                TextType::class,
                [
                    'label' => 'Code postal',
                ]
            )
            ->add('city')
            ->add('email', EmailType::class)
            ->add(
                'web_site',
                UrlType::class,
                [
                    'label' => 'Site web',
                    'required' => false,
                ]
            )
            ->add('phone')
            ->add('mobile')
            ->add('fax')
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Description de l\'association',
                    'help' => 'Décrivez brièvement votre association, max 600 caractères',
                    'attr' => [
                        'rows' => 8,
                    ],
                ]
            )
            ->add(
                'requirement',
                TextareaType::class,
                [
                    'help' => 'Besoins permanents',
                    'required' => false,
                ]
            )
            ->add(
                'place',
                TextareaType::class,
                [
                    'help' => 'Où se situent les activités',
                    'required' => false,
                ]
            )
            ->add(
                'contact',
                TextareaType::class,
                [
                    'help' => 'Autres informations de contact',
                    'required' => false,
                ]
            )
            ->add(
                'secteurs',
                EntityType::class,
                array(
                    'class' => Secteur::class,
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                )
            )
            ->add(
                'image',
                FileType::class,
                array(
                    'label' => "Photo",
                    'required' => false,
                )
            )
            ->add(
                'fileFile',
                FileType::class,
                array(
                    'label' => "Fichier",
                    'required' => false,
                )
            )
            ->add(
                'fileDescriptif',
                TextType::class,
                array(
                    'label' => "Description du fichier",
                    'required' => false,
                    'help' => 'Si vous ajoutez un fichier',
                )
            )->add(
                'mailing',
                CheckboxType::class,
                [
                    'help' => 'Décochez la case pour ne plus recevoir de mail lorsqu\'un volontaire s\'inscris',
                    'required' => false,
                ]
            )
            ->add(
                'valider',
                CheckboxType::class,
                [
                    'help' => 'Cochez la case pour que celle-ci apparaisse sur le site',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            array(
                'data_class' => Association::class,
            )
        );
    }
}
