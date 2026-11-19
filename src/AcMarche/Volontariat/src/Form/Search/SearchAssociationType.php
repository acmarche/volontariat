<?php

namespace AcMarche\Volontariat\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchAssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                SearchType::class,
                [
                    'attr' => [
                        'placeholder' => 'Nom',
                    ],
                    'label_attr' => ['class' => 'sr-only'],
                    'required' => false,
                ]
            )
            ->add(
                'city',
                SearchType::class,
                [
                    'label' => 'Localité',
                    'attr' => [
                        'placeholder' => 'Localité ou code postal',
                    ],
                    'label_attr' => ['class' => 'sr-only'],
                    'required' => false,
                ]
            )
            ->add(
                'createdAt',
                DateType::class,
                [
                    'label' => 'Inscrit à partir du',
                    'attr' => [
                        'placeholder' => 'Nom',
                    ],
                    'label_attr' => ['class' => 'sr-only'],
                    'required' => false,
                ]
            )
            ->add(
                'valider',
                ChoiceType::class,
                [
                    'label' => 'Validée',
                    'choices' => ['Toute' => 2,'Oui' => 1, 'Non' => false],
                    'label_attr' => ['class' => 'sr-only'],
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([]);
    }
}
