<?php

declare(strict_types=1);

use AcMarche\Volontariat\Doctrine\RandFunction;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $containerConfigurator->extension(
        'doctrine',
        [
            'orm' => [
                'mappings' => [
                    'AcMarche\Volontariat' => [
                        'is_bundle' => false,
                        'dir' => '%kernel.project_dir%/src/AcMarche/Volontariat/src/Entity',
                        'prefix' => 'AcMarche\Volontariat',
                        'alias' => 'AcMarcheVolontariat',
                    ],
                ],
                'dql' => [
                    'numeric_functions' => [
                        'Rand' => RandFunction::class,
                    ],
                ],
            ],
        ]
    );
};
