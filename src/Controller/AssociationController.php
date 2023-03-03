<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Form\Search\SearchAssociationType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/association')]
class AssociationController extends AbstractController
{
    public function __construct(private AssociationRepository $associationRepository, private FileHelper $fileHelper)
    {
    }

    #[Route(path: '/', name: 'volontariat_association')]
    public function indexAction(Request $request): Response
    {
        $session = $request->getSession();
        $data = array();

        $search_form = $this->createForm(SearchAssociationType::class, $data);
        $search_form->handleRequest($request);
        if ($search_form->isSubmitted() && $search_form->isValid()) {
            if ($search_form->get('raz')->isClicked()) {
                $this->addFlash('info', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('volontariat_association');
            }

            $data = $search_form->getData();
        }
        $associations = $this->associationRepository->search($data);
        foreach ($associations as $association) {
            $association->setImages($this->fileHelper->getImages($association));
        }

        return $this->render(
            '@Volontariat/association/index.html.twig',
            array(
                'search_form' => $search_form->createView(),
                'associations' => $associations,
            )
        );
    }

    #[Route(path: '/{id}', name: 'volontariat_association_show')]
    public function showAction(Association $association): Response
    {
        $images = $this->fileHelper->getImages($association);

        return $this->render('@Volontariat/association/show.html.twig', array(
            'association' => $association,
            'blog' => true,
            'images' => $images,
        ));
    }
}
