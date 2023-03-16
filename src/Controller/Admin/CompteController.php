<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use AcMarche\Volontariat\Repository\UserRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/compte')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class CompteController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route(path: '/{id}', name: 'volontariat_admin_compte_delete', methods: ['DELETE'])]
    public function deleteAction(Request $request, $id) : RedirectResponse
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entity = $this->userRepository->find($id);

            if ($entity === null) {
                throw $this->createNotFoundException('Unable to find Compte entity.');
            }

            $this->userRepository->remove($entity);
            $this->addFlash("success", "Le compte a bien été supprimé");
        }
        return $this->redirectToRoute('volontariat_admin_utilisateur');
    }

    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_admin_compte_delete', array('id' => $id)))
            ->setMethod(Request::METHOD_DELETE)
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
