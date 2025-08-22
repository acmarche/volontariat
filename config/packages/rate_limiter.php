<?php

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $frameworkConfig): void {

    $frameworkConfig->rateLimiter()
        ->limiter('anonymous_api')
        ->policy('fixed_window')
        ->limit(15)
        ->interval('60 minutes');
};
