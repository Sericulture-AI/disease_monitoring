<?php
/**
 * Copy this file to config.local.php and fill it in. config.local.php is
 * listed in .gitignore, so the SMTP password never reaches GitHub.
 * Only the keys you put here override config.php.
 */
return [
    'mail' => [
        'from_email' => 'yourname@gmail.com',
        'always_notify' => [
            // 'pathology@cmerti.res.in',
        ],
        'smtp' => [
            'host'     => 'smtp.gmail.com',
            'port'     => 465,
            'secure'   => 'ssl',
            'username' => 'yourname@gmail.com',
            'password' => 'xxxx xxxx xxxx xxxx',   // Gmail App Password
        ],
    ],
];
