<?php

namespace AcMarche\Volontariat\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ImageDropZoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
      /*  $builder->add('file', DropzoneType::class, [
            'attr' => [
                'placeholder' => 'Cliquez ici pour sélectioner les images',
            ],
            'label' => false,
            'multiple' => true,
            'constraints' => [
                 new File([
                    'maxSize' => '8000k',
                    'mimeTypes' => [
                        'image/*',
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger une image valide',
                ]),
            ],
        ]);*/
    }
}
