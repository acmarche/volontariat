<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->extension(
        'liip_imagine',
        [
            'filter_sets' => [
                'cache' => null,
                'acmarche_volontariat_thumb' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [120, 90], 'mode' => 'outbound']],
                ],
                'acmarche_volontariat_400_270' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [400, 270], 'mode' => 'outbound']],
                ],
                'acmarche_volontariat_740_380' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [740, 380], 'mode' => 'outbound']],
                ],
                'acmarche_volontariat_200_100' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [200, 100], 'mode' => 'outbound']],
                ],
                'acmarche_volontariat_edit_thumb' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [200, 150], 'mode' => 'outbound']],
                ],
                'acmarche_volontariat_zoom_thumb' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [1200, 900], 'mode' => 'inset']],
                ],
                'acmarche_volontariat_news_thumb' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [973, 615], 'mode' => 'outbound']],
                ],
                'acmarche_volontariat_banner' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [1920, 700], 'mode' => 'outbound']],
                ],
                'acmarche_volontariat_sidebar_thumb' => [
                    'quality' => 100,
                    'cache' => 'default',
                    'filters' => ['thumbnail' => ['size' => [973, 615], 'mode' => 'outbound']],
                ],
            ],
        ]
    );
};
