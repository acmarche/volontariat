<?php

namespace AcMarche\Volontariat\Form\User;

use AcMarche\Volontariat\Entity\Security\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'accord',
                CheckboxType::class,
                [
                    'label' => 'J\'ai pris connaissance du règlement de la vie privée',
                    'help' => '<a href="https://volontariat.marche.be/page/vieprive" target="_blank">Consulter le règlement</a>',
                    'help_html' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
            )
        );
    }

    public function getParent(): ?string
    {
        return RegisterVoluntaryType::class;
    }


}
