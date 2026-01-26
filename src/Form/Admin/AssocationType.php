<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Secteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AssocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'label' => 'Rue et numéro',
            ])
            ->add(
                'postalCode',
                TextType::class,
                [
                    'label' => 'Code postal',
                ]
            )
            ->add('city', TextType::class, [
                'label' => 'Localité',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Courriel',
                'help' => 'zeez',
            ])
            ->add(
                'web_site',
                UrlType::class,
                [
                    'label' => 'Site web',
                    'required' => false,
                ]
            )
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('mobile', TextType::class, [
                'label' => 'Mobile',
                'required' => false,
            ])
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => "Description de l'association",
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
                    'label' => 'Besoins en volontariat',
                    'help' => 'Besoins permanents',
                    'required' => false,
                ]
            )
            ->add(
                'secteurs',
                EntityType::class,
                [
                    'class' => Secteur::class,
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
            ->add(
                'image',
                VichImageType::class,
                [
                    'label' => 'Image',
                    'required' => false,
                ]
            )
            ->add(
                'notification_new_voluntary',
                CheckboxType::class,
                [
                    'label' => 'Notification volontaire',
                    'help' => 'Décochez la case pour ne plus recevoir de mail lorsqu\'un volontaire s\'inscris',
                    'required' => false,
                ]
            )
            ->add(
                'notification_message_association',
                CheckboxType::class,
                [
                    'label' => 'Notification générale',
                    'help' => 'Décochez la case pour ne plus recevoir de mail de la plate-forme',
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

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Association::class,
            ]
        );
    }
}
