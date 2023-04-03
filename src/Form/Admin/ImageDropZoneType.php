<?php

namespace AcMarche\Volontariat\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ImageDropZoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', DropzoneType::class, [
            'attr' => [
                'placeholder' => 'Cliquez ici pour sélectioner les images',
            ],
            'label' => false,
            'multiple' => true,
        ]);
    }
}
