<?php

namespace AcMarche\Volontariat\Form\Search;

use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Entity\Vehicule;
use AcMarche\Volontariat\Entity\Volontaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchVolontaireType extends AbstractType
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $secteurs = $this->entityManager->getRepository(Secteur::class)->getForSearch();
        $vehicules = $this->entityManager->getRepository(Vehicule::class)->getForSearch();
        $cities = $this->entityManager->getRepository(Volontaire::class)->getLocalitesForSearch();

        $builder
            ->add(
                'nom',
                SearchType::class,
                array(
                    'attr' => array(
                        'placeholder' => "Nom",
                    ),
                    'label_attr' => ['class' => 'sr-only'],
                    'required' => false,
                )
            )
            ->add(
                'secteurs',
                ChoiceType::class,
                array(
                    'choices' => $secteurs,
                    'required' => false,
                    'multiple' => true,
                    'label_attr' => ['class' => 'sr-only'],
                )
            )
            ->add(
                'vehicule',
                ChoiceType::class,
                array(
                    'placeholder' => 'Véhicule',
                    'choices' => $vehicules,
                    'required' => false,
                    'label_attr' => ['class' => 'sr-only'],
                )
            )
            ->add(
                'city',
                ChoiceType::class,
                array(
                    'placeholder' => 'Localité',
                    'choices' => $cities,
                    'required' => false,
                    'label_attr' => ['class' => 'sr-only'],
                )
            )
            ->add(
                'createdAt',
                DateType::class,
                [
                    'required' => false,
                    'widget' => 'single_text',
                    'label' => 'Inscrit depuis le',
                    'attr' => ['title' => 'Inscrit depuis le']
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'Rechercher',
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array());
    }
}
