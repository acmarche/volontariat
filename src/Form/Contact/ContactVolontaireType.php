<?php

namespace AcMarche\Volontariat\Form\Contact;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactVolontaireType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'association_nom',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Nom de votre association',
                ]
            );
    }

    public function getParent()
    {
        return ContactBaseType::class;
    }


}
