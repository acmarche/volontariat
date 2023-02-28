<?php

declare(strict_types=1);

use AcMarche\Volontariat\Doctrine\RandFunction;
use Symfony\Config\DoctrineConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\Env;

return static function (DoctrineConfig $doctrine) {

    $doctrine->dbal()
        ->connection('default')
        ->url(env('DATABASE_URL')->resolve())
        ->charset('utf8mb4');

    $emMda = $doctrine->orm()->entityManager('default');
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
    ]);
};
