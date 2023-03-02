<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\User\UtilisateurEditType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


#[Route(path: '/compte')]
#[IsGranted('ROLE_VOLONTARIAT')]
class CompteController extends AbstractController
{
    public function __construct(private TokenStorageInterface $tokenStorage, private ManagerRegistry $managerRegistry)
    {
    }

    #[Route(path: '/', name: 'volontariat_compte_home')]
    public function indexAction(Request $request): Response
    {
        $user = $this->getUser();
        $formProfil = $this->createForm(UtilisateurEditType::class, $user);
        $formProfil->handleRequest($request);
        if ($formProfil->isSubmitted() && $formProfil->isValid()) {
            $this->userManager->updateUser();
            $this->addFlash('success', 'Profil mis à jour');

            return $this->redirectToRoute('volontariat_compte_home');
        }

        return $this->render(
            '@Volontariat/dashboard/settings/index.html.twig',
            [
                'user' => $user,
                'tab_active' => 'profil',
                'form_profil' => $formProfil->createView(),
            ]
        );
    }

    #[Route(path: '/delete', name: 'volontariat_backend_utilisateur_delete', methods: ['GET', 'DELETE'])]
    public function deleteAction(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $volontaires = $em->getRepository(Volontaire::class)->findBy(['user' => $user]);
            foreach ($volontaires as $volontaire) {
                $em->remove($volontaire);
            }

            $associations = $em->getRepository(Association::class)->findBy(['user' => $user]);

            foreach ($associations as $association) {
                $em->remove($association);
            }

            $em->remove($user);
            $em->flush();

            $this->tokenStorage->setToken(null);

            $this->addFlash("success", "L'utilisateur a bien été supprimé");

            return $this->redirectToRoute('app_logout');
        }

        return $this->render(
            '@Volontariat/dashboard/delete.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    private function createDeleteForm(User $user): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('volontariat_backend_utilisateur_delete', array('id' => $user->getId())))
            ->setMethod(Request::METHOD_DELETE)
            ->add('submit', SubmitType::class, array('label' => 'Delete', 'attr' => array('class' => 'btn-danger')))
            ->getForm();
    }
}
