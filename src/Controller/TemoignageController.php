<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Temoignage;
use AcMarche\Volontariat\Form\Admin\TemoignageType;
use AcMarche\Volontariat\Repository\TemoignageRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/temoignage")
 *
 *
 */
class TemoignageController extends AbstractController
{
    /**
     * @var TemoignageRepository
     */
    private $temoignageRepository;

    public function __construct(TemoignageRepository $temoignageRepository)
    {
        $this->temoignageRepository = $temoignageRepository;
    }

    /**
     * @Route("/", name="volontariat_temoignage", methods={"GET"})
     */
    public function index(): Response
    {
        $temoignages = $this->temoignageRepository->findAll();

        return $this->render(
            '@Volontariat/temoignage/index.html.twig',
            ['temoignages' => $temoignages]
        );
    }
}
