<?php

namespace AcMarche\Volontariat\Form\Contact;

use AcMarche\Volontariat\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReferencerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $froms = $options['froms'];

        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Votre nom',
                ]
            )
            ->add(
                'froms',
                ChoiceType::class,
                [
                    'label' => 'Votre mail',
                    'choices' => $froms,
                ]
            )
            ->add(
                'nom_destinataire',
                TextType::class,
                [
                    'label' => 'Nom de l\'Association ou du volontaire',
                ]
            )
            ->add(
                'destinataires',
                EmailType::class,
                [
                    'label' => 'Mail de l\'Association ou du volontaire',
                ]
            )
            ->add(
                'contenu',
                TextareaType::class,
                array(
                    'label' => 'Votre texte',
                    'attr' => array('rows' => 8),
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Message::class,
                'froms' => [],
            )
        );

        $resolver->setRequired('froms');
    }
}
