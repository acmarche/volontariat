<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Admin\AssocierAssociationType;
use AcMarche\Volontariat\Form\Admin\AssocierVolontaireType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Service\FormBuilderVolontariat;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/link")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class LinkController extends AbstractController
{
    /**
     * @var AssociationRepository
     */
    private $associationRepository;
    /**
     * @var VolontaireRepository
     */
    private $volontaireRepository;
    /**
     * @var FormBuilderVolontariat
     */
    private $formBuilder;

    public function __construct(
        AssociationRepository $associationRepository,
        VolontaireRepository $volontaireRepository,
        FormBuilderVolontariat $formBuilder
    ) {
        $this->associationRepository = $associationRepository;
        $this->volontaireRepository = $volontaireRepository;
        $this->formBuilder = $formBuilder;
    }

    /**
     * Displays a form to create a new Compte entity.
     *
     * @Route("/volontaire/{id}", name="volontariat_admin_associer_volontaire",methods={"GET","POST"})
     *
     */
    public function associerVolontaireAction(Request $request, Volontaire $volontaire)
    {
        $form = $this->createForm(AssocierVolontaireType::class)
            ->add('submit', SubmitType::class, array('label' => 'Associer'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $data->getUsers();

            $volontaire->setUser($user);

            $this->volontaireRepository->save();

            $this->addFlash("success", "Le volontaire à bien été associé au compte");

            return $this->redirectToRoute('volontariat_admin_volontaire_show', ['id' => $volontaire->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/link/volontaire.html.twig',
            array(
                'volontaire' => $volontaire,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Displays a form to create a new Compte entity.
     *
     * @Route("/association/{id}", name="volontariat_admin_associer_association", methods={"GET","POST"})
     *
     */
    public function associerAssociationAction(Request $request, Association $association)
    {
        $form = $this->createForm(AssocierAssociationType::class)
            ->add('submit', SubmitType::class, array('label' => 'Associer'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $data->getUsers();

            $association->setUser($user);

            $this->associationRepository->save();

            $this->addFlash("success", "L' association à bien été associée au compte");

            return $this->redirectToRoute('volontariat_admin_association_show', ['id' => $association->getId()]);
        }

        return $this->render(
            '@Volontariat/admin/link/asbl.html.twig',
            array(
                'association' => $association,
                'form' => $form->createView(),
            )
        );
    }

    /**
     *
     * @Route("/volontaire/dissocier/{id}", name="volontariat_admin_dissocier_volontaire", methods={"DELETE"})
     */
    public function dissocierVolontaireAction(Request $request, Volontaire $volontaire)
    {
        $form = $this->formBuilder->createDissocierForm($volontaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $volontaire->setUser(null);

            $this->volontaireRepository->save();
            $this->addFlash('success', 'Le compte ont bien été dissocié');
        }

        return $this->redirectToRoute('volontariat_admin_volontaire_show', array('id' => $volontaire->getId()));
    }

    /**
     * Deletes a  entity.
     *
     * @Route("/association/dissocier/{id}", name="volontariat_admin_dissocier_association", methods={"DELETE"})
     */
    public function dissocierAssociationAction(Request $request, Association $association)
    {
        $form = $this->formBuilder->createDissocierForm($association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $association->setUser(null);
            $this->associationRepository->save();

            $this->addFlash('success', 'Le compte ont bien été dissociée');
        }

        return $this->redirectToRoute('volontariat_admin_association_show', array('id' => $association->getId()));
    }

}
