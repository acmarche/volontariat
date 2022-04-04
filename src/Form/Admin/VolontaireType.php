<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Entity\Vehicule;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\SecteurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VolontaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'civility',
                ChoiceType::class,
                array(
                    'choices' => array(
                        "Mademoiselle" => "Mademoiselle",
                        "Madame" => "Madame",
                        "Monsieur" => "Monsieur",
                    ),
                    'required' => false,
                )
            )
            ->add('name')
            ->add('surname')
            ->add('address')
            ->add(
                'number',
                TextType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'postalCode',
                IntegerType::class,
                array(
                    'required' => false,
                    'label' => 'Code postal'
                )
            )
            ->add('city', TextType::class, array('required' => true))
            ->add('email', EmailType::class)
            ->add('phone')
            ->add('mobile')
            ->add('fax')
            ->add('birthday', BirthdayType::class, array('years' => range(1930, date('Y'))))
            ->add(
                'job',
                TextType::class,
                [
                    'required' => false,
                    'help' => 'Votre profession actuelle ou ancienne'
                ]
            )
            ->add(
                'secteur',
                TextType::class,
                [
                    'label' => 'Secteur autre',
                    'required' => false,
                ]
            )
            ->add(
                'availability',
                TextType::class,
                [
                    'help' => 'Le soir, le we, la journée...',
                ]
            )
            ->add(
                'vehicules',
                EntityType::class,
                array(
                    'class' => Vehicule::class,
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                )
            )
            ->add(
                'known_by',
                TextType::class,
                [
                    'label' => 'Comment avez vous connu la plate-forme',
                    'required' => false,
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Décrivez votre motivation',
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
                    'query_builder' => fn(SecteurRepository $er) => $er->secteursActifs()
                ]
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
                'inactif',
                CheckboxType::class,
                [
                    'label' => "Je suis indisponible pour le moment",
                    'required' => false,
                ]
            );
        //   ->add('association')
        //   ->add('user', RegistrationFormType::class)
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            array(
                'data_class' => Volontaire::class,
            )
        );
    }
}
