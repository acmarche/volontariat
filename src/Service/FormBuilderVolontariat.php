<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/10/18
 * Time: 14:18
 */

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

class FormBuilderVolontariat
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(RouterInterface $router, FormFactoryInterface $formFactory)
    {
        $this->router = $router;
        $this->formFactory = $formFactory;
    }

    /**
     * @param Association|Volontaire $object
     * @param string $type
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createDissocierForm($object)
    {
        $url = $this->router->generate('volontariat_admin_dissocier_volontaire', array('id' => $object->getId()));

        if ($object instanceof Association) {
            $url = $this->router->generate('volontariat_admin_dissocier_association', array('id' => $object->getId()));
        }

        return $this->formFactory->createBuilder()
            ->setAction($url)
            ->setMethod('DELETE')
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