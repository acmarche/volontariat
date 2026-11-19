<?php

declare(strict_types=1);

use AcMarche\Volontariat\Doctrine\MonthFunction;
use AcMarche\Volontariat\Doctrine\RandFunction;
use AcMarche\Volontariat\Doctrine\YearFunction;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'connections' => [
                'default' => [
                    'url' => '%env(resolve:DATABASE_URL)%',
                    'charset' => 'utf8mb4',
                ],
            ],
        ],
        'orm' => [
            'entity_managers' => [
                'default' => [
                    'connection' => 'default',
                    'mappings' => [
                        'AcMarcheVolontariat' => [
                            'is_bundle' => false,
                            'type' => 'attribute',
                            'dir' => '%kernel.project_dir%/src/AcMarche/Volontariat/src/Entity',
                            'prefix' => 'AcMarche\Volontariat',
                        ],
                    ],
                    'dql' => [
                        'numeric_functions' => [
                            'Rand' => RandFunction::class,
                        ],
                        'datetime_functions' => [
                            'YEAR' => YearFunction::class,
                            'MONTH' => MonthFunction::class,
                        ],
                    ],
                ],
            ],
        ],
    ]);
};
