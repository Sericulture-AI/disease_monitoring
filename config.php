<?php
/**
 * =============================================================================
 * Disease Monitoring — settings
 * =============================================================================
 *
 * Shared settings (committed to GitHub). Put passwords in config.local.php,
 * which is never committed — copy config.local.example.php to start it.
 *
 * SIGN-IN
 * Staff sign in with the SAME username and password they use for the work
 * diary on cmerti-site. The accounts are read from the same database table
 * (diary_users); nothing in cmerti-site is changed. The database settings are
 * therefore taken straight from cmerti-site/includes/db-config.php, so moving
 * the site to a live server is still a one-file edit over there.
 *
 * E-MAIL
 * XAMPP cannot send mail on its own. Fill in the 'smtp' block below (an
 * institute mail account, or a Gmail account with an App Password) and every
 * record that is saved is e-mailed as a PDF to the addresses typed on the
 * form. Until it is filled in, records still save and the PDF can still be
 * downloaded; the record simply shows "e-mail not sent" with a Resend button.
 * -----------------------------------------------------------------------------
 */

$__main = dirname(__DIR__) . '/cmerti-site/includes/db-config.php';

$__cfg = [

    /* ------------------------------------------------------------ database
       Same MySQL database as cmerti-site. Override any key here if this
       module ever has to run against a different server. */
    'db' => (is_file($__main) ? (array) require $__main : []) + [
        'host' => '127.0.0.1', 'port' => 3306, 'name' => 'cmerti_site',
        'user' => 'root', 'pass' => '', 'charset' => 'utf8mb4', 'prefix' => 'diary_',
    ],

    /* Prefix for this module's own tables (dm_records, dm_findings, …).
       Different from the diary's prefix, so the two never collide. */
    'table_prefix' => 'dm_',

    /* Where cmerti-site lives, for the "change your password" link. */
    'main_site_url' => '/cmerti-site',

    /* ------------------------------------------------------------- session */
    'session_minutes' => 120,     // idle sign-out
    'login_attempts'  => 5,       // failures from one address …
    'login_lockout'   => 15,      // … before it waits this many minutes

    /* -------------------------------------------------------------- e-mail */
    'mail' => [
        'from_email' => '',                       // e.g. 'diseasemonitoring@cmerti.res.in'
        'from_name'  => 'CMER&TI Disease Monitoring',

        /* Addresses that receive EVERY record, in addition to those typed on
           the form — e.g. the Pathology section. */
        'always_notify' => [
            // 'pathology@cmerti.res.in',
        ],

        /* Also send a copy to the person who recorded it (their e-mail on
           the user account). */
        'copy_to_recorder' => true,

        /* SMTP. Leave 'host' empty to fall back to PHP's mail(), which on
           XAMPP only works if sendmail has been configured.

           Gmail:   host smtp.gmail.com, port 465, secure 'ssl',
                    username = the Gmail address, password = a 16-letter
                    App Password (Google Account → Security → App passwords).
           NIC mail: host smtp.mgovcloud.in (or as NIC advises), port 587,
                    secure 'tls'. */
        'smtp' => [
            'host'     => '',
            'port'     => 465,
            'secure'   => 'ssl',      // 'ssl' (465), 'tls' (587, STARTTLS) or ''
            'username' => '',
            'password' => '',
            'timeout'  => 20,
        ],
    ],

    /* ------------------------------------------------------------- uploads */
    'storage_dir'   => 'storage',     // PDFs and photos; closed to the web
    'photo_max_mb'  => 5,

    /* ------------------------------------------------------------ severity
       DEFAULT working thresholds for grading a finding from its incidence.
       These are NOT taken from the institute's pages — set them with the
       Pathology section. Diseases listed under 'zero_tolerance' are graded
       High at any incidence above zero (pebrine: an infected lot is rejected). */
    'severity' => [
        'moderate_from' => 5,     // % incidence
        'high_from'     => 15,    // % incidence
        'zero_tolerance' => ['muga_pebrine', 'eri_pebrine'],
    ],

    /* Indicative muscardine-weather flag (muga). Based on the Lahdoigarh
       comparison on the Silkworm Diseases page: Dec 2012, minimum 5.5 °C with
       RH 72–92 % → epidemic; Dec 2016, minimum 10.0 °C with RH 44–82 % → nil. */
    'muscardine_weather' => [
        'min_temp_below' => 10.0,
        'rh_max_at_least' => 72,
    ],
];

/* Passwords and anything machine-specific go in config.local.php, which is
   NOT committed to GitHub (see .gitignore). Copy config.local.example.php to
   config.local.php and fill it in; its values override the ones above. */
$__local = __DIR__ . '/config.local.php';
return is_file($__local) ? array_replace_recursive($__cfg, (array) require $__local) : $__cfg;
