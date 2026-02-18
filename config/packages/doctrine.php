<?php

declare(strict_types=1);

use AcMarche\Volontariat\Doctrine\MonthFunction;
use AcMarche\Volontariat\Doctrine\RandFunction;
use AcMarche\Volontariat\Doctrine\YearFunction;
use Symfony\Config\DoctrineConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\Env;

return static function (DoctrineConfig $doctrineConfig): void {

    $doctrineConfig->dbal()
        ->connection('default')
        ->url(env('DATABASE_URL')->resolve())
        ->charset('utf8mb4');

    $emMda = $doctrineConfig->orm()->entityManager('default');
    $emMda->connection('default');
    $emMda->mapping('AcMarcheVolontariat')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/AcMarche/Volontariat/src/Entity')
        ->prefix('AcMarche\Volontariat')
        ->alias('AcMarcheVolontariat');
    $emMda->dql([
        'numeric_functions' => [
            'Rand' => RandFunction::class,
        ],
        'datetime_functions' => [
            'YEAR' => YearFunction::class,
            'MONTH' => MonthFunction::class,
        ],
    ]);
};
