<?php


use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Security\VolontariatAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->extension('security', [
        'password_hashers' => [
            User::class => [
                'algorithm' => 'auto',
            ],
        ],
    ]);

    $containerConfigurator->extension(
        'security',
        [
            'providers' => [
                'merite_user_provider' => [
                    'entity' => [
                        'class' => User::class,
                        'property' => 'username',
                    ],
                ],
            ],
        ]
    );

    $authenticators = [VolontariatAuthenticator::class];

    $main = [
        'provider' => 'merite_user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'form_login' => [],
        'entry_point' => VolontariatAuthenticator::class,
        'switch_user' => true,
    ];

    $main['custom_authenticator'] = $authenticators;

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                'main' => $main,
            ],
        ]
    );
};
