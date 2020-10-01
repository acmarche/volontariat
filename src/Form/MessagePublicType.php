<?php

namespace AcMarche\Volontariat\Form;

use AcMarche\Volontariat\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessagePublicType extends AbstractType
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
                'froms',
                ChoiceType::class,
                [
                    'label'=>'De',
                    'choices' => $froms,
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Message::class,
                'froms'=>[]
            )
        );

        $resolver->setRequired('froms');
    }
}
