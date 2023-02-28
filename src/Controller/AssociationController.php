<?php

namespace AcMarche\Volontariat\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Form\Search\SearchAssociationType;
use AcMarche\Volontariat\Service\FileHelper;
use AcMarche\Volontariat\Service\VolontariatConstante;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route(path: '/association')]
class AssociationController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper, private ManagerRegistry $managerRegistry)
    {
    }
    #[Route(path: '/', name: 'volontariat_association')]
    public function indexAction(Request $request) : Response
    {
        $em = $this->managerRegistry->getManager();
        $session = $request->getSession();
        $data = array();
        $key = VolontariatConstante::ASSOCIATION_SEARCH;
        if ($session->has($key)) {
            $data = unserialize($session->get($key));
        }
        $search_form = $this->createForm(SearchAssociationType::class, $data);
        $search_form->handleRequest($request);
        if ($search_form->isSubmitted() && $search_form->isValid()) {
            if ($search_form->get('raz')->isClicked()) {
                $session->remove($key);
                $this->addFlash('info', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('volontariat_association');
            }

            $data = $search_form->getData();
        }
        $session->set($key, serialize($data));
        $associations = $em->getRepository(Association::class)->search($data);
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
    /**
     * Finds and displays a Association entity.
     *
     *
     */
    #[Route(path: '/{id}', name: 'volontariat_association_show')]
    public function showAction(Association $association) : Response
    {
        $images = $this->fileHelper->getImages($association);
        return $this->render('@Volontariat/association/show.html.twig', array(
            'association' => $association,
            'blog' => true,
            'images' => $images,
        ));
    }
}
