<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\SecteurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;
use Vich\UploaderBundle\Form\Type\VichImageType;

class VolontaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('surname')
            ->add(
                'postalCode',
                IntegerType::class,
                [
                    'required' => false,
                    'label' => 'Code postal',
                ]
            )
            ->add('city', TextType::class, [
                'required' => true,
                'label' => 'Localité',
            ])
            ->add('email', EmailType::class)
            ->add('mobile', TextType::class, [
                'required' => false,
                'label' => 'Téléphone',
            ])
            ->add('birthyear', IntegerType::class, [
                'required' => false,
                'label' => 'Année de naissance',
                'constraints' => [
                    new Range(min: 1955, max: 2020),
                ],
            ])
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Décrivez votre motivation',
                    'attr' => ['rows' => 4],
                ]
            )
            ->add(
                'secteurs',
                EntityType::class,
                [
                    'class' => Secteur::class,
                    'label' => 'Secteurs d\'activités',
                    'choice_label' => 'toStringWithDescription',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'query_builder' => fn(SecteurRepository $er) => $er->secteursActifs(),
                ]
            )
            ->add(
                'image',
                VichImageType::class,
                [
                    'label' => 'Photo',
                    'required' => false,
                ]
            )
            ->add(
                'inactif',
                CheckboxType::class,
                [
                    'label' => 'Je suis indisponible pour le moment',
                    'required' => false,
                ]
            )
            ->add(
                'notification_new_association',
                CheckboxType::class,
                [
                    'label' => 'Nouvelle association',
                    'help' => 'Être notifié lorsqu\'une association s\'est inscrite',
                    'required' => false,
                ]
            )
            ->add(
                'notification_message_association',
                CheckboxType::class,
                [
                    'label' => 'Solliciation Asbl',
                    'help' => 'Être notifié lorsqu\'une association envoie un message',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Volontaire::class,
            ]
        );
    }
}
