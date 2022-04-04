<?php

namespace AcMarche\Volontariat\Form\Search;

use AcMarche\Volontariat\Entity\Secteur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchAssociationType extends AbstractType
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $secteurs = $this->entityManager->getRepository(Secteur::class)->getForSearch();

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
                'secteur',
                ChoiceType::class,
                array(
                    'choices' => $secteurs,
                    'required' => false,
                    'placeholder' => "Secteur",
                    'label_attr' => ['class' => 'sr-only']
                )
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
