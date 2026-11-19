<?php

declare(strict_types=1);

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Security\MessageDigestPasswordHasher;
use AcMarche\Volontariat\Security\VolontariatAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'providers' => [
            'admin_provider' => [
                'entity' => ['class' => User::class, 'manager_name' => 'default', 'property' => 'email'],
            ],
            'association_provider' => [
                'entity' => ['class' => Association::class, 'manager_name' => 'default', 'property' => 'email'],
            ],
            'volontaire_provider' => [
                'entity' => ['class' => Volontaire::class, 'manager_name' => 'default', 'property' => 'email'],
            ],
            'all_users' => [
                'chain' => ['providers' => ['association_provider', 'volontaire_provider', 'admin_provider']],
            ],
        ],
        'password_hashers' => [
            'cap_hasher' => ['id' => MessageDigestPasswordHasher::class],
            User::class => ['algorithm' => 'sodium', 'migrate_from' => ['cap_hasher']],
            Association::class => ['algorithm' => 'sodium', 'migrate_from' => ['cap_hasher']],
            Volontaire::class => ['algorithm' => 'sodium', 'migrate_from' => ['cap_hasher']],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'lazy' => true,
                'form_login' => [
                    'login_path' => 'app_login',
                    'remember_me' => true,
                    'enable_csrf' => true,
                ],
                'logout' => [
                    'path' => 'app_logout',
                ],
                'custom_authenticators' => [VolontariatAuthenticator::class],
                'provider' => 'all_users',
                'entry_point' => VolontariatAuthenticator::class,
                'login_throttling' => [
                    'max_attempts' => 8,
                    'interval' => '15 minutes',
                ],
                'remember_me' => [
                    'secret' => '%kernel.secret%',
                    'lifetime' => 604800,
                    'path' => '/',
                    'always_remember_me' => true,
                ],
            ],
        ],
    ]);
};
