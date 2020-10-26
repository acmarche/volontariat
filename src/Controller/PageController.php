<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
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
     * @Route("/page/{slug}",name="volontariat_page_show")
     *
     */
    public function show(Page $page)
    {
        $images = $this->fileHelper->getImages($page);

        return $this->render(
            '@Volontariat/page/show.html.twig',
            [
                'page' => $page,
                'images' => $images,
            ]
        );
    }
}
