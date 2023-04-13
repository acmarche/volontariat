<?php

declare(strict_types=1);

use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('locale', 'fr');
    $parameters->set('acmarche_volontariat_email_from', 'volontariat@marche.be');
    $parameters->set('acmarche_volontariat_email_to', 'volontariat@marche.be');
    $parameters->set('acmarche_volontariat_upload_directory', '%kernel.project_dir%/public/uploads/volontariat');
    $parameters->set('acmarche_volontariat_download_directory', '/uploads/volontariat');
    $parameters->set('acmarche_volontariat_webpath', '%kernel.project_dir%');
    $parameters->set('acmarche_volontariat_captcha_site_key', '%env(RECAPTCHA_SITE_KEY)%');
    $parameters->set('acmarche_volontariat_captcha_secret_key', '%env(RECAPTCHA_SECRET_KEY)%');

    $services = $containerConfigurator->services();
    $services = $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$from', '%acmarche_volontariat_email_from%')
        ->bind('$rootUploadPath', '%acmarche_volontariat_upload_directory%')
        ->bind('$rootDownloadPath', '%acmarche_volontariat_download_directory%');

    $services->load('AcMarche\Volontariat\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Migrations,Tests,Kernel.php,DataFixtures}']);

    $services->set(TokenManager::class)
        ->arg('$formLoginAuthenticator', service('security.authenticator.form_login.main'));
};
