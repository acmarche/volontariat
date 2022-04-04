<?php

namespace AcMarche\Volontariat\Controller;

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
 *
 * @Route("/volontaire")
 */
class VolontaireController extends AbstractController
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var VolontaireRepository
     */
    private $volontaireRepository;
    /**
     * @var VolontaireService
     */
    private $volontaireService;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        VolontaireRepository $volontaireRepository,
        VolontaireService $volontaireService,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->volontaireRepository = $volontaireRepository;
        $this->volontaireService = $volontaireService;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @Route("/", name="volontariat_volontaire")
     *
     */
    public function indexAction(Request $request)
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
     * @Route("/{id}", name="volontariat_volontaire_show")
     *
     */
    public function showAction(Volontaire $volontaire)
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
