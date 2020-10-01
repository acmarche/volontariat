<?php

namespace AcMarche\Volontariat\Form\User;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Security\SecurityData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UtilisateurEditType extends AbstractType
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove("username")
            ->remove("plainPassword");

        if ($this->authorizationChecker->isGranted(SecurityData::getRoleAdmin())) {
            $builder->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => SecurityData::getRoles(),
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
        }
    }

    public function getParent()
    {
        return UtilisateurType::class;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
            )
        );
    }
}
