<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Enum\StatisticTypeEnum;
use AcMarche\Volontariat\Form\Admin\StatisticFilterType;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\BesoinRepository;
use AcMarche\Volontariat\Repository\SecteurRepository;
use AcMarche\Volontariat\Repository\StatisticRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\RolesEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::admin->value)]
#[Route(path: '/admin/statistic')]
class StatisticController extends AbstractController
{
    public function __construct(
        private readonly StatisticRepository $statisticRepository,
        private readonly AssociationRepository $associationRepository,
        private readonly VolontaireRepository $volontaireRepository,
        private readonly BesoinRepository $besoinRepository,
        private readonly SecteurRepository $secteurRepository,
    ) {
    }

    #[Route(path: '/', name: 'volontariat_admin_statistic', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(StatisticFilterType::class);
        $form->handleRequest($request);

        $year = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $year = $data['year'] ?: null;
        }

        $contactStats = [];
        foreach (StatisticTypeEnum::cases() as $type) {
            $contactStats[] = [
                'type' => $type,
                'label' => $type->label(),
                'count' => $this->statisticRepository->countByTypeAndYear($type, $year),
                'monthly' => $this->buildMonthlyData(
                    $this->statisticRepository->countByTypeGroupedByMonth($type, $year),
                ),
            ];
        }

        $associations = $this->associationRepository->findAll();
        $associationsValidated = $this->associationRepository->findActif();
        $volontaires = $this->volontaireRepository->findAll();
        $volontairesActif = $this->volontaireRepository->findActif();
        $besoins = $this->besoinRepository->findAll();

        $secteurs = $this->secteurRepository->findAllOrdered();
        $secteursPopular = [];
        foreach ($secteurs as $secteur) {
            $volCount = count($secteur->getVolontaires());
            $assocCount = count($secteur->getAssociations());
            $secteursPopular[] = [
                'name' => $secteur->getName(),
                'volontaires' => $volCount,
                'associations' => $assocCount,
                'total' => $volCount + $assocCount,
            ];
        }
        usort($secteursPopular, fn(array $a, array $b) => $b['total'] <=> $a['total']);
        $secteursPopular = array_slice($secteursPopular, 0, 10);

        return $this->render(
            '@Volontariat/admin/statistic/index.html.twig',
            [
                'form' => $form,
                'year' => $year,
                'contactStats' => $contactStats,
                'totalAssociations' => count($associations),
                'totalAssociationsValidated' => count($associationsValidated),
                'totalVolontaires' => count($volontaires),
                'totalVolontairesActif' => count($volontairesActif),
                'totalBesoins' => count($besoins),
                'secteursPopular' => $secteursPopular,
                'months' => $this->getMonthNames(),
            ],
        );
    }

    private function buildMonthlyData(array $rawMonthly): array
    {
        $data = array_fill(1, 12, 0);
        foreach ($rawMonthly as $row) {
            $data[(int) $row['month']] = (int) $row['total'];
        }

        return $data;
    }

    private function getMonthNames(): array
    {
        return [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre',
        ];
    }
}
