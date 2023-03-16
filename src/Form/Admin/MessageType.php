<?php

namespace AcMarche\Volontariat\Form\Admin;

use AcMarche\Volontariat\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $query = $options['query'];
        $builder
            ->add(
                'sujet',
                TextType::class
            )
            ->add(
                'contenu',
                TextareaType::class,
                [
                    'attr' => ['rows' => 8],
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'Pièce jointe',
                    'required' => false,
                ]
            );

        if (!$query) {
            $builder->add(
                'selection_destinataires',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisissez les destinataires',
                    'choices' => ['Associations' => 'association', 'Volontaires' => 'volontaire'],
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Message::class,
                'query' => null,
            ]
        );

        $resolver->setAllowedTypes('query', ['string', 'null']);
    }
}
