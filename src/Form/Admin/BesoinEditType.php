<?php

namespace AcMarche\Volontariat\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class BesoinEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'forceSend',
                CheckboxType::class,
                [
                    'label' => 'Envoyer un rappel',
                    'help' => 'Si vous cochez la case, un mail sera envoyé à tous les volontaires',
                    'required' => false,
                    'attr' => ['class' => 'checkbox22'],
                ]
            );
    }

    public function getParent(): string
    {
        return BesoinType::class;
    }
}
