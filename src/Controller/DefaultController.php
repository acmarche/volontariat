<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\PageRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Search\Searcher;
use AcMarche\Volontariat\Service\FileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        private FileHelper $fileHelper,
        private PageRepository $pageRepository,
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private Searcher $searcher
    ) {
    }

    #[Route(path: '/', name: 'volontariat_home')]
    public function index(): Response
    {
        $pages = $this->pageRepository->findRecentNews();
        foreach ($pages as $page) {
            $page->images = $this->fileHelper->getImages($page);
        }
        $volontaires = $this->volontaireRepository->getRecent();
        $associations = $this->associationRepository->getRecent();

        return $this->render('@Volontariat/default/index.html.twig', [
            'pages' => $pages,
            'volontaires' => $volontaires,
            'associations' => $associations,
        ]);
    }

    #[Route(path: '/search', name: 'volontariat_search')]
    public function search(Request $request): Response
    {
        $keyword = $request->query->get('keyword');
        $results = [];
        if (!$keyword) {
            $this->addFlash('danger', 'Veuillez encoder un mot clef');
        } else {
            $results = $this->searcher->search($keyword);
        }

        return $this->render('@Volontariat/default/search.html.twig', [
            'keyword' => $keyword,
            'results' => $results,
        ]);
    }
}
