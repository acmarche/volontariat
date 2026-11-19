<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', [
        'form_themes' => ['bootstrap_5_layout.html.twig'],
        'paths' => [
            '%kernel.project_dir%/src/AcMarche/Volontariat/templates' => 'Volontariat',
            '%kernel.project_dir%/src/AcMarche/Volontariat/public/assets/img' => 'imagesV',
            '%kernel.project_dir%/src/AcMarche/Volontariat/public/assets/css' => 'cssV',
        ],
        'globals' => [
            'bootcdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css',
        ],
    ]);
};
