<?php

namespace AcMarche\Volontariat\Search;

use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\BesoinRepository;
use AcMarche\Volontariat\Repository\PageRepository;
use Symfony\Component\Routing\RouterInterface;

class Searcher
{
    public const searchAssocations = 'searchAssociation';
    public const searchVolontaires = 'searchVolontaire';

    public function __construct(
        private AssociationRepository $associationRepository,
        private BesoinRepository $besoinRepository,
        private PageRepository $pageRepository,
        private RouterInterface $router
    ) {
    }

    /**
     * @return ResultDto[]
     */
    public function search(string $keyword): array
    {
        $results = [];
        foreach ($this->associationRepository->searchFront($keyword) as $association) {
            $results[] = new ResultDto(
                $association->name,
                $association->description,
                $this->router->generate('volontariat_association_show', ['slug' => $association->getSlug()])
            );
        }

        foreach ($this->pageRepository->search($keyword) as $page) {
            $results[] = new ResultDto(
                $page->title,
                $page->excerpt,
                $this->router->generate('volontariat_page_show', ['slug' => $page->getSlug()])
            );
        }

        foreach ($this->besoinRepository->search($keyword) as $besoin) {
            $results[] = new ResultDto(
                $besoin->getName(),
                $besoin->getRequirement(),
                $this->router->generate('volontariat_besoin_show', ['id' => $besoin->getId()])
            );
        }

        return $results;
    }
}
