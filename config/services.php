<?php

declare(strict_types=1);

use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('locale', 'fr');

    $services = $containerConfigurator->services();
    $services = $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('AcMarche\Volontariat\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Migrations,Tests,Kernel.php,DataFixtures}']);

    $services->set(TokenManager::class)
        ->arg('$formLoginAuthenticator', service('security.authenticator.form_login.main'));
};
