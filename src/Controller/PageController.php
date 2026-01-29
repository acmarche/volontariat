<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    public function __construct(private FileHelper $fileHelper)
    {
    }

    #[Route(path: '/page/{slug}', name: 'volontariat_page_show')]
    public function show(#[MapEntity(expr: 'repository.findOneBySlug(slug)')] Page $page): Response
    {
        $images = $this->fileHelper->getImages($page);
        $documents = $this->fileHelper->getDocuments($page);

        return $this->render(
            '@Volontariat/page/show.html.twig',
            [
                'page' => $page,
                'images' => $images,
                'documents' => $documents,
            ]
        );
    }
}
