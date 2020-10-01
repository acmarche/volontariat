<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Form\Search\SearchAssociationType;
use AcMarche\Volontariat\Service\FileHelper;
use AcMarche\Volontariat\Service\VolontariatConstante;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AssociationController
 * @package AppBundle\Controller
 * @Route("/association")
 */
class AssociationController extends AbstractController
{
    /**
     * @var FileHelper
     */
    private $fileHelper;

    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    /**
     * @Route("/", name="volontariat_association")
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
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
            'association/index.html.twig',
            array(
                'search_form' => $search_form->createView(),
                'associations' => $associations,
            )
        );
    }

    /**
     * Finds and displays a Association entity.
     *
     * @Route("/{id}", name="volontariat_association_show")
     *
     */
    public function showAction(Association $association)
    {
        $images = $this->fileHelper->getImages($association);

        return $this->render('association/show.html.twig', array(
            'association' => $association,
            'blog' => true,
            'images' => $images,
        ));
    }
}
