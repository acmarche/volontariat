<?php

declare(strict_types=1);

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig) {
    $twig
        ->formThemes(['bootstrap_5_layout.html.twig'])
        ->path('%kernel.project_dir%/src/AcMarche/Volontariat/templates', 'Volontariat')
        ->path('%kernel.project_dir%/src/AcMarche/Volontariat/public/assets/img', 'images')
        ->path('%kernel.project_dir%/src/AcMarche/Volontariat/public/assets/css', 'styles')
        ->global('bootcdn')->value('https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css');
};
