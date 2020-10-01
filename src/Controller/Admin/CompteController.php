<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/compte")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 */
class CompteController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }


    /**
     * Deletes a Compte entity.
     *
     * @Route("/{id}", name="volontariat_admin_compte_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entity = $this->userRepository->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Compte entity.');
            }

            $this->userRepository->remove($entity);
            $this->addFlash("success", "Le compte a bien été supprimé");
        }

        return $this->redirectToRoute('volontariat_admin_utilisateur');
    }

    /**
     * Creates a form to delete a Compte entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_compte_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
