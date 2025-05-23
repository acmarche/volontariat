<?php

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Security\MessageDigestPasswordHasher;
use AcMarche\Volontariat\Security\VolontariatAuthenticator;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security
        ->provider('volontariat_user_provider')
        ->entity()
        ->class(User::class)
        ->managerName('default')
        ->property('email');

    $security
        ->passwordHasher('cap_hasher')
        ->id(MessageDigestPasswordHasher::class);

    $security
        ->passwordHasher(User::class)
        ->algorithm('sodium')
        ->migrateFrom(['cap_hasher']);

    $security
        ->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false);

    $mainFirewall = $security
        ->firewall('main')
        ->lazy(true);

    $mainFirewall
        ->formLogin()
        ->loginPath('app_login')
        ->rememberMe(true)
        ->enableCsrf(true);

    $mainFirewall
        ->logout()
        ->path('app_logout');

    $authenticators = [VolontariatAuthenticator::class];

    $mainFirewall
        ->customAuthenticators($authenticators)
        ->provider('volontariat_user_provider')
        ->entryPoint(VolontariatAuthenticator::class)
        ->loginThrottling()
        ->maxAttempts(6)
        ->interval('15 minutes');

    $mainFirewall
        ->rememberMe([
            'secret' => '%kernel.secret%',
            'lifetime' => 604800,
            'path' => '/',
            'always_remember_me' => true,
        ]);

    $security->roleHierarchy('ROLE_VOLONTARIAT_ADMIN', ['ROLE_VOLONTARIAT_ASSOCIATION']);
};
