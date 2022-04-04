<?php

namespace AcMarche\Volontariat\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Service\FileHelper;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper, private ManagerRegistry $managerRegistry)
    {
    }

    #[Route(path: '/', name: 'volontariat_home')]
    public function index() : Response
    {
        $em = $this->managerRegistry->getManager();
        $args = ['valider' => true];
        $activites = $em->getRepository(Activite::class)->findBy($args);
        $pages = $em->getRepository(Page::class)->findRecent();
        foreach ($activites as $activite) {
            $activite->setImages($this->fileHelper->getImages($activite));
        }
        foreach ($pages as $page) {
            $page->setImages($this->fileHelper->getImages($page));
        }
        $volontaires = $em->getRepository(Volontaire::class)->getRecent();
        $associations = $em->getRepository(Association::class)->getRecent();
        $besoins = $em->getRepository(Besoin::class)->getRecent();
        return $this->render('@Volontariat/default/index.html.twig', [
            'activites' => $activites,
            'pages' => $pages,
            'volontaires' => $volontaires,
            'besoins' => $besoins,
            'associations' => $associations,
        ]);
    }

    #[Route(path: '/contact', name: 'volontariat_contact')]
    public function contact() : Response
    {
        return $this->render('@Volontariat/default/contact.html.twig', []);
    }
}
