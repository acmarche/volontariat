<?php

namespace AcMarche\Volontariat\Form\Contact;

use AcMarche\Volontariat\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReferencerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'sujet',
                TextType::class,
                [
                    'label' => 'Votre nom',
                ]
            )
            ->add(
                'from',
                TextType::class,
                [
                    'label' => 'Votre mail',
                ]
            )
            ->add(
                'to',
                EmailType::class,
                [
                    'label' => 'Adresse mail du destinataire',
                ]
            )
            ->add(
                'contenu',
                TextareaType::class,
                [
                    'label' => 'Votre texte',
                    'attr' => ['rows' => 8],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Message::class,
            ]
        );
    }
}
