<?php

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Enum\StatisticTypeEnum;
use AcMarche\Volontariat\Entity\Statistic;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\StatisticRepository;

class StatisticService
{
    public function __construct(private readonly StatisticRepository $statisticRepository)
    {
    }

    public function log(StatisticTypeEnum $type, ?Association $association = null, ?Volontaire $volontaire = null): void
    {
        $statistic = new Statistic();
        $statistic->type = $type;
        $statistic->association = $association;
        $statistic->volontaire = $volontaire;

        $this->statisticRepository->insert($statistic);
    }
}
