<?php

namespace AcMarche\Volontariat\Form\Contact;

use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Manager\ContactManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactBaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Votre nom',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true,
                    'label' => 'Votre email',
                ]
            )
            ->add(
                'sujet',
                TextType::class
            )
            ->add(
                'contenu',
                TextareaType::class,
                array(
                    'attr' => array('rows' => 8),
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            array(
                'data_class' => ContactManager::class,
            )
        );

    }
}
