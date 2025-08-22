<?php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $frameworkConfig): void {
    $frameworkConfig->router()->defaultUri('https://volontariat.marche.be');
};