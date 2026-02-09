<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\BesoinRepository;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AssociationController extends AbstractController
{
    public function __construct(
        private AssociationRepository $associationRepository,
        private BesoinRepository $besoinRepository,
        private FileHelper $fileHelper
    ) {
    }

    #[Route(path: '/association/', name: 'volontariat_association')]
    public function index(Request $request): Response
    {
        $associations = $this->associationRepository->findActif();

        foreach ($associations as $association) {
            $association->setImages($this->fileHelper->getImages($association));
        }

        return $this->render(
            '@Volontariat/association/index.html.twig',
            [
                'associations' => $associations,
            ]
        );
    }

    #[Route(path: '/association/{slug}', name: 'volontariat_association_show')]
    public function show(
        #[MapEntity(expr: 'repository.findOneBySlug(slug)')]
        Association $association
    ): Response {
        $images = $this->fileHelper->getImages($association);
        $besoins = $this->besoinRepository->findByAssociation($association);

        return $this->render('@Volontariat/association/show.html.twig', [
            'association' => $association,
            'besoins' => $besoins,
            'images' => $images,
        ]);
    }
}
