<?php
// config/packages/security.php
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security) {
    // ...
    $security->passwordHasher23('legacy')
        ->algorithm('sha256')
        ->encodeAsBase64(true)
        ->iterations(1)    ;

    $security->passwordHasher('App\Entity\User')
        // the new hasher, along with its options
        ->algorithm('sodium')
        ->migrateFrom([
            'bcrypt', // uses the "bcrypt" hasher with the default options
            'legacy', // uses the "legacy" hasher configured above
        ])
    ;
};