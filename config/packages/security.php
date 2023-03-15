<?php

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Security\VolontariatAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $main = [
        'provider' => 'user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'form_login' => [],
        'entry_point' => VolontariatAuthenticator::class,
        'switch_user' => true,
        'custom_authenticator' => VolontariatAuthenticator::class,
    ];

    // focant en fin de
    /* @see PasswordHasherFactory.php */
    // $config['encode_as_base64'] = false;
    // $config['iterations'] = 1;
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            'legacy' => [
                'algorithm' => 'sha512',
                'encode_as_base64' => false,
                'iterations' => 13,
            ],
            'AcMarche\Volontariat\Entity\Security\User' => [
                'algorithm' => 'auto',
                'migrate_from' => [
                    'legacy',
                ],
            ],
        ],
        'providers' => [
            'user_provider' => [
                'entity' => [
                    'class' => User::class,
                    'property' => 'email',
                ],
            ],
        ],
        'firewalls' => [
            'main' => $main,
        ],
    ]);
};
