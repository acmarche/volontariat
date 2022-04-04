<?php

namespace AcMarche\Volontariat\Controller;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Search\SearchVolontaireType;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Service\VolontaireService;
use AcMarche\Volontariat\Service\VolontariatConstante;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class VolontaireController
 */
#[Route(path: '/volontaire')]
class VolontaireController extends AbstractController
{
    private Session $session;
    public function __construct(private VolontaireRepository $volontaireRepository, private VolontaireService $volontaireService, private AuthorizationCheckerInterface $authorizationChecker)
    {
    }
    #[Route(path: '/', name: 'volontariat_volontaire')]
    public function indexAction(Request $request) : Response
    {
        $data = array();
        $session = $request->getSession();
        $key = VolontariatConstante::VOLONTAIRE_SEARCH;
        $search = false;
        if ($session->has($key)) {
            $data = unserialize($session->get($key));
            $search = true;
        }
        $session = $request->getSession();
        $search_form = $this->createForm(SearchVolontaireType::class, $data);
        $search_form->handleRequest($request);
        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            $search = true;
            $session->set($key, serialize($data));
        }
        $volontaires = $this->volontaireRepository->search($data);
        if (!$this->authorizationChecker->isGranted('index')) {
            return $this->render(
                '@Volontariat/volontaire/index_not_connected.html.twig',
                array(
                    'volontaires' => $volontaires,
                )
            );
        }
        return $this->render(
            '@Volontariat/volontaire/index.html.twig',
            array(
                'search_form' => $search_form->createView(),
                'volontaires' => $volontaires,
                'search' => $search,
            )
        );
    }
    /**
     * Finds and displays a Volontaire entity.
     *
     *
     */
    #[Route(path: '/{id}', name: 'volontariat_volontaire_show')]
    public function showAction(Volontaire $volontaire) : Response
    {
        $associations = $this->volontaireService->getAssociationsWithSameSecteur($volontaire);
        if (!$this->authorizationChecker->isGranted('show', $volontaire)) {
            return $this->render(
                '@Volontariat/volontaire/show_not_connected.html.twig',
                array(
                    'volontaire' => $volontaire,
                    'associations' => $associations,
                )
            );
        }
        return $this->render(
            '@Volontariat/volontaire/show.html.twig',
            array(
                'volontaire' => $volontaire,
                'associations' => $associations,
            )
        );
    }
}
