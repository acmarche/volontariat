<?php

namespace AcMarche\Volontariat\Form;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class FormBuilderVolontariat
{
    public function __construct(private RouterInterface $router, private FormFactoryInterface $formFactory)
    {

    }

    public function createDissocierForm(Association|Volontaire $object): FormInterface
    {
        $url = $this->router->generate('volontariat_admin_dissocier_volontaire', array('id' => $object->getId()));

        if ($object instanceof Association) {
            $url = $this->router->generate('volontariat_admin_dissocier_association', array('id' => $object->getId()));
        }

        return $this->formFactory->createBuilder()
            ->setAction($url)
            ->setMethod(Request::METHOD_DELETE)
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'Dissocier',
                    'attr' => array('class' => 'btn-danger btn-sm hidden-print float-right'),
                )
            )
            ->getForm();
    }
}