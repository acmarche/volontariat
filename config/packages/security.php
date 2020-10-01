<?php


use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Security\AppAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'security',
        [
            'encoders' => [
                User::class => [
                    'algorithm' => 'sha512',
                    'encode_as_base64' => false,
                    'iterations' => 1,
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
                    'guard' => ['authenticators' => [AppAuthenticator::class]],
                    'logout' => ['path' => 'app_logout'],
                ],
            ],
        ]
    );
};
