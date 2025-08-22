<?php

namespace AcMarche\Volontariat\Form\Search;

use AcMarche\Volontariat\Entity\Volontaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchVolontaireType extends AbstractType
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $cities = $this->entityManager->getRepository(Volontaire::class)->getLocalitesForSearch();

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
                ChoiceType::class,
                [
                    'placeholder' => 'Localité',
                    'choices' => $cities,
                    'required' => false,
                    'label_attr' => ['class' => 'sr-only'],
                ]
            )
            ->add(
                'createdAt',
                DateType::class,
                [
                    'required' => false,
                    'widget' => 'single_text',
                    'label' => 'Inscrit depuis le',
                    'attr' => ['title' => 'Inscrit depuis le'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([]);
    }
}
