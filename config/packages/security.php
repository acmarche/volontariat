<?php


use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Security\VolontariatAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->extension(
        'security',
        [
            'password_hashers' => [
                User::class => [
                    'algorithm' => 'auto',
                ],
            ],
        ]
    );

    $containerConfigurator->extension(
        'security',
        [
            'providers' => [
                'app_user_provider' => [
                    'entity' => ['class' => User::class, 'property' => 'email'],
                ],
            ],
        ]
    );

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                'main' => [
                    'custom_authenticator' => [VolontariatAuthenticator::class],
                    'logout' => ['path' => 'app_logout'],
                ],
            ],
        ]
    );
};
