<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\SecteurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class VolontaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('surname')
            ->add('address')
            ->add(
                'number',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'postalCode',
                IntegerType::class,
                [
                    'required' => false,
                    'label' => 'Code postal',
                ]
            )
            ->add('city', TextType::class, ['required' => true])
            ->add('email', EmailType::class)
            ->add('mobile')
            ->add('birthday', BirthdayType::class, ['years' => range(1930, date('Y'))])
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Décrivez votre motivation',
                    'attr' => ['rows' => 8],
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
                'photo',
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
