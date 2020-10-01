<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Service\FileHelper;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @package AppBundle\Controller
 * @Route("/activite")
 *
 */
class ActiviteController extends AbstractController
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
     * Liste des activités
     * @Route("/",name="volontariat_activite")
     *
     * @param FileHelper $this ->fileHelper
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $activites = $em->getRepository(Activite::class)->findAll();

        foreach ($activites as $activite) {
            $activite->setImages($this->fileHelper->getImages($activite));
        }

        return $this->render('@Volontariat/activite/show.html.twig', [
            'activites' => $activites,
        ]);
    }

    /**
     * Displays a form to edit an existing Volontaire entity.
     *
     * @Route("/{id}", name="volontariat_activite_show", methods={"GET"})
     *
     */
    public function showAction(Activite $activite)
    {
        $images = $this->fileHelper->getImages($activite);

        return $this->render('@Volontariat/activite/show.html.twig', array(
            'association' => $activite->getAssociation(),
            'images' => $images,
            'activite' => $activite,
        ));
    }
}
