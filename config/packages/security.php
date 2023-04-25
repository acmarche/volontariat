<?php

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Security\MessageDigestPasswordHasher;
use AcMarche\Volontariat\Security\VolontariatAuthenticator;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security) {
    $has512 = [
        'algorithm' => 'sha512',
        'encode_as_base64' => false,
        'iterations' => 1,
    ];

    /*  $security->passwordHasher(Md5VerySecureHasher::class, $has512)
          ->hashAlgorithm('sha512')
          ->encodeAsBase64(false)
          ->iterations(1)
          ->migrateFrom('legacy')
          ;*/

    //  $security->passwordHasher(MessageDigestPasswordHasher::class, $has512);

    $security->passwordHasher('cap_hasher')
        ->id(MessageDigestPasswordHasher::class);

    $security->passwordHasher(User::class)
        ->algorithm('auto')
        ->migrateFrom(['cap_hasher']);

    /* $security->passwordHasher(User::class, $has512)
     ->encodeAsBase64(false);*/

    $security->provider('user_provider', [
        'entity' => [
            'class' => User::class,
            'property' => 'email',
        ],
    ]);

    $main = [
        'provider' => 'user_provider',
        'logout' => ['path' => 'app_logout'],
        'form_login' => [],
        //   'switch_user' => true,
        'entry_point' => VolontariatAuthenticator::class,
        'custom_authenticators' => [VolontariatAuthenticator::class],
    ];

    $security->firewall('main', $main)
        ->lazy(true);
};
