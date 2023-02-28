<?php

namespace AcMarche\Volontariat\Controller;

use Doctrine\Persistence\ManagerRegistry;
use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Service\FileHelper;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/activite')]
class ActiviteController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper, private ManagerRegistry $managerRegistry)
    {
    }
    /**
     * Liste des activités
     *
     * @param FileHelper $this ->fileHelper
     */
    #[Route(path: '/', name: 'volontariat_activite')]
    public function indexAction() : Response
    {
        $em = $this->managerRegistry->getManager();
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
     *
     */
    #[Route(path: '/{id}', name: 'volontariat_activite_show', methods: ['GET'])]
    public function showAction(Activite $activite) : Response
    {
        $images = $this->fileHelper->getImages($activite);
        return $this->render('@Volontariat/activite/show.html.twig', array(
            'association' => $activite->getAssociation(),
            'images' => $images,
            'activite' => $activite,
        ));
    }
}
