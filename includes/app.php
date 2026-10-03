<?php
/**
 * =============================================================================
 * Disease Monitoring — core: settings, database, sign-in, form tokens
 * =============================================================================
 *
 * Sign-in follows cmerti-site/includes/auth.php exactly — same accounts table,
 * same password hashes, same lockout after repeated failures, same CSRF token
 * on every form, same idle sign-out — but with this module's own session
 * cookie, because a cookie issued for /cmerti-site/ is not sent to
 * /disease-monitoring/. Nothing in cmerti-site is read except its database
 * settings, and nothing there is written except the sign-in log it already
 * keeps.
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';     // ROOT_URL, esc(), page_link(), asset()

/* =============================================================== settings */

function dm_config(): array
{
    static $c = null;
    if ($c === null) $c = (array) require dirname(__DIR__) . '/config.php';
    return $c;
}

function dm_setting(string $key, $default = null)
{
    $v = dm_config();
    foreach (explode('.', $key) as $k) {
        if (!is_array($v) || !array_key_exists($k, $v)) return $default;
        $v = $v[$k];
    }
    return $v;
}

/** URL of a page of this module. */
function dm_url(string $page = '', array $q = []): string
{
    $u = ROOT_URL . '/' . ltrim($page, '/');
    return $q ? $u . '?' . http_build_query($q) : $u;
}

/* =============================================================== database */

function dm_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $c = (array) dm_setting('db', []);
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $c['host'] ?? '127.0.0.1', (int) ($c['port'] ?? 3306),
        $c['name'] ?? '', $c['charset'] ?? 'utf8mb4');

    $pdo = new PDO($dsn, (string) ($c['user'] ?? ''), (string) ($c['pass'] ?? ''), [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

function dm_db_ready(): bool
{
    static $r = null;
    if ($r !== null) return $r;
    try { dm_db()->query('SELECT 1'); $r = true; } catch (Throwable $e) { $r = false; }
    return $r;
}

/** The diary's tables (users, login_log, audit_log) — shared, read for sign-in. */
function dm_diary_table(string $name): string
{
    return (string) dm_setting('db.prefix', 'diary_') . $name;
}

/** This module's own tables. */
function dm_table(string $name): string
{
    return (string) dm_setting('table_prefix', 'dm_') . $name;
}

function dm_run(string $sql, array $a = []): PDOStatement
{
    $st = dm_db()->prepare($sql);
    $st->execute($a);
    return $st;
}
function dm_all(string $sql, array $a = []): array   { return dm_run($sql, $a)->fetchAll(); }
function dm_one(string $sql, array $a = []): ?array  { $r = dm_run($sql, $a)->fetch(); return $r === false ? null : $r; }
function dm_value(string $sql, array $a = [], $d = null) { $v = dm_run($sql, $a)->fetchColumn(); return $v === false ? $d : $v; }

/** Absolute path inside the private storage folder, created if missing. */
function dm_storage(string $sub = ''): string
{
    $base = dirname(__DIR__) . '/' . trim((string) dm_setting('storage_dir', 'storage'), '/');
    $dir  = $sub === '' ? $base : $base . '/' . trim($sub, '/');
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    if (!is_file($base . '/.htaccess')) {
        @file_put_contents($base . '/.htaccess',
            "# Private: files are handed out only by pdf.php and photo.php after sign-in.\n"
          . "<IfModule mod_authz_core.c>\n  Require all denied\n</IfModule>\n"
          . "<IfModule !mod_authz_core.c>\n  Order allow,deny\n  Deny from all\n</IfModule>\n");
    }
    return $dir;
}

/* ================================================================ install */

/**
 * Create this module's tables if they are not there yet. Only dm_ tables;
 * the diary's tables are never created, altered or dropped from here.
 */
function dm_install(): void
{
    static $done = false;
    if ($done) return;
    $done = true;

    $flag = dm_storage() . '/.installed-v1';
    if (is_file($flag)) return;

    $r = dm_table('records'); $f = dm_table('findings'); $n = dm_table('notifications');

    dm_db()->exec("CREATE TABLE IF NOT EXISTS `$r` (
        `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `ref_no`         VARCHAR(24)  NOT NULL,
        `user_id`        INT UNSIGNED NOT NULL,
        `recorder_name`  VARCHAR(120) NOT NULL,
        `obs_date`       DATE         NOT NULL,
        `crop`           ENUM('muga','eri') NOT NULL,
        `crop_season`    VARCHAR(40)  DEFAULT NULL,
        `site_type`      VARCHAR(40)  NOT NULL,
        `stage`          VARCHAR(40)  NOT NULL,
        `state`          VARCHAR(60)  NOT NULL,
        `district`       VARCHAR(80)  NOT NULL,
        `block`          VARCHAR(80)  DEFAULT NULL,
        `village`        VARCHAR(120) DEFAULT NULL,
        `site_name`      VARCHAR(160) DEFAULT NULL,
        `farmer_name`    VARCHAR(120) DEFAULT NULL,
        `farmer_phone`   VARCHAR(20)  DEFAULT NULL,
        `latitude`       DECIMAL(9,6) DEFAULT NULL,
        `longitude`      DECIMAL(9,6) DEFAULT NULL,
        `host_plant`     VARCHAR(40)  DEFAULT NULL,
        `dfls`           INT UNSIGNED DEFAULT NULL,
        `sample_size`    INT UNSIGNED DEFAULT NULL,
        `mme_examined`   INT UNSIGNED DEFAULT NULL,
        `mme_positive`   INT UNSIGNED DEFAULT NULL,
        `temp_min`       DECIMAL(4,1) DEFAULT NULL,
        `temp_max`       DECIMAL(4,1) DEFAULT NULL,
        `rh_min`         TINYINT UNSIGNED DEFAULT NULL,
        `rh_max`         TINYINT UNSIGNED DEFAULT NULL,
        `rainfall_mm`    DECIMAL(6,1) DEFAULT NULL,
        `overall`        ENUM('none','low','moderate','high') NOT NULL DEFAULT 'none',
        `flags`          TEXT         DEFAULT NULL,
        `action_taken`   TEXT         DEFAULT NULL,
        `remarks`        TEXT         DEFAULT NULL,
        `notify_emails`  TEXT         DEFAULT NULL,
        `photo`          VARCHAR(120) DEFAULT NULL,
        `pdf_file`       VARCHAR(120) DEFAULT NULL,
        `created_at`     DATETIME     NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_ref` (`ref_no`),
        KEY `ix_user` (`user_id`),
        KEY `ix_date` (`obs_date`),
        KEY `ix_district` (`district`),
        KEY `ix_overall` (`overall`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    dm_db()->exec("CREATE TABLE IF NOT EXISTS `$f` (
        `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `record_id`     INT UNSIGNED NOT NULL,
        `disease_key`   VARCHAR(40)  NOT NULL,
        `incidence_pct` DECIMAL(5,2) NOT NULL,
        `severity`      ENUM('low','moderate','high') NOT NULL,
        PRIMARY KEY (`id`),
        KEY `ix_record` (`record_id`),
        KEY `ix_disease` (`disease_key`),
        CONSTRAINT `fk_dm_find_rec` FOREIGN KEY (`record_id`) REFERENCES `$r` (`id`) ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    dm_db()->exec("CREATE TABLE IF NOT EXISTS `$n` (
        `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `record_id`  INT UNSIGNED NOT NULL,
        `email`      VARCHAR(160) NOT NULL,
        `status`     ENUM('sent','failed') NOT NULL,
        `error`      VARCHAR(400) DEFAULT NULL,
        `sent_by`    INT UNSIGNED DEFAULT NULL,
        `sent_at`    DATETIME     NOT NULL,
        PRIMARY KEY (`id`),
        KEY `ix_record` (`record_id`),
        CONSTRAINT `fk_dm_note_rec` FOREIGN KEY (`record_id`) REFERENCES `$r` (`id`) ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    @file_put_contents($flag, date('c'));
}

/* ================================================================ session */

const DM_SESSION_NAME = 'CMERTIDMSESS';

function dm_session_start(): void
{
    if (PHP_SAPI === 'cli' || session_status() === PHP_SESSION_ACTIVE || headers_sent()) return;
    $https = (($_SERVER['HTTPS'] ?? '') !== '' && ($_SERVER['HTTPS'] ?? '') !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => ROOT_URL !== '' ? ROOT_URL . '/' : '/',
        'httponly' => true,
        'secure'   => $https,
        'samesite' => 'Lax',
    ]);
    session_name(DM_SESSION_NAME);
    session_start();
}

/** Start the session only if the visitor already has one. */
function dm_session_boot(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    if (!isset($_COOKIE[DM_SESSION_NAME])) return;
    dm_session_start();
}

function dm_fingerprint(): string
{
    return substr(hash('sha256', (string) ($_SERVER['HTTP_USER_AGENT'] ?? '') . '|' . php_uname('n')), 0, 32);
}

/** The signed-in user (row from diary_users), or null. Re-read every request. */
function dm_user(): ?array
{
    static $u = null, $done = false;
    if ($done) return $u;
    $done = true;

    dm_session_boot();
    if (session_status() !== PHP_SESSION_ACTIVE || empty($_SESSION['dm_uid'])) return null;

    if (!hash_equals((string) ($_SESSION['dm_fp'] ?? ''), dm_fingerprint())) { dm_logout(); return null; }
    if (time() - (int) ($_SESSION['dm_born'] ?? 0) > 12 * 3600)               { dm_logout(); return null; }
    $idle = (int) dm_setting('session_minutes', 120) * 60;
    if ($idle > 0 && time() - (int) ($_SESSION['dm_seen'] ?? 0) > $idle)      { dm_logout(); return null; }
    $_SESSION['dm_seen'] = time();

    try {
        $row = dm_one('SELECT * FROM ' . dm_diary_table('users') . ' WHERE id = ? AND is_active = 1',
                      [(int) $_SESSION['dm_uid']]);
    } catch (Throwable $e) { return null; }
    if (!$row) { dm_logout(); return null; }

    return $u = $row;
}

function dm_is_admin(?array $u = null): bool
{
    $u = $u ?? dm_user();
    return $u !== null && ($u['role'] ?? '') === 'admin';
}

function dm_private_page(): void
{
    if (headers_sent()) return;
    header('Cache-Control: no-store, no-cache, must-revalidate, private');
    header('Pragma: no-cache');
    header('X-Robots-Tag: noindex, nofollow');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

/** Every page of the module calls this first. */
function dm_require_login(): array
{
    dm_private_page();
    if (!dm_db_ready()) dm_fatal('No database connection. Check the database settings in cmerti-site/includes/db-config.php (read by config.php here).');
    dm_install();

    $u = dm_user();
    if ($u) return $u;

    dm_session_start();
    $_SESSION['dm_after'] = $_SERVER['REQUEST_URI'] ?? '';
    header('Location: ' . dm_url('login.php'));
    exit;
}

function dm_name(array $u): string
{
    return trim((string) ($u['full_name'] ?? '')) ?: (string) ($u['username'] ?? '');
}

/* ----------------------------------------------------- sign in / sign out */

function dm_ip_hash(): string
{
    /* Same salt as cmerti-site, so failures here and there count together. */
    return substr(hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0') . '|cmerti-diary'), 0, 16);
}

function dm_login(string $username, string $password): string
{
    $username = trim($username);
    if ($username === '' || $password === '') return 'Enter both your username and your password.';

    $ip   = dm_ip_hash();
    $max  = (int) dm_setting('login_attempts', 5);
    $mins = (int) dm_setting('login_lockout', 15);
    $log  = dm_diary_table('login_log');

    try {
        $fails = (int) dm_value("SELECT COUNT(*) FROM `$log` WHERE ip_hash = ? AND succeeded = 0
                                 AND attempted_at > (NOW() - INTERVAL $mins MINUTE)", [$ip], 0);
    } catch (Throwable $e) { $fails = 0; }
    if ($max > 0 && $fails >= $max) {
        return "Too many failed attempts. Try again in {$mins} minutes, or ask an administrator to reset your password.";
    }

    $row = dm_one('SELECT * FROM ' . dm_diary_table('users') . ' WHERE username = ? LIMIT 1', [$username]);
    $ok  = $row && password_verify($password, (string) $row['password_hash']);

    try {
        dm_run("INSERT INTO `$log` (user_id, username, ip_hash, succeeded, attempted_at) VALUES (?, ?, ?, ?, NOW())",
               [$row['id'] ?? null, mb_substr($username, 0, 60), $ip, $ok ? 1 : 0]);
        if ($ok) dm_run("DELETE FROM `$log` WHERE ip_hash = ? AND succeeded = 0", [$ip]);
    } catch (Throwable $e) { /* logging never blocks a sign-in */ }

    if (!$ok) { dm_audit('login_failed', 'user', $row ? (int) $row['id'] : null, 'disease-monitoring; username: ' . $username); return 'That username and password do not match.'; }
    if ((int) $row['is_active'] !== 1) return 'That account has been disabled. Please contact an administrator.';
    if ((int) ($row['must_change_password'] ?? 0) === 1) {
        return 'You are still using the password issued by the administrator. Sign in to the work diary on the main website once and set your own password, then come back here.';
    }

    dm_session_start();
    session_regenerate_id(true);
    $_SESSION['dm_uid']  = (int) $row['id'];
    $_SESSION['dm_seen'] = time();
    $_SESSION['dm_born'] = time();
    $_SESSION['dm_fp']   = dm_fingerprint();
    $_SESSION['dm_csrf'] = bin2hex(random_bytes(32));
    dm_audit('login', 'user', (int) $row['id'], 'disease-monitoring');
    return '';
}

function dm_logout(): void
{
    dm_session_boot();
    if (session_status() !== PHP_SESSION_ACTIVE) return;
    $_SESSION = [];
    $p = session_get_cookie_params();
    setcookie(session_name(), '', ['expires' => time() - 42000, 'path' => $p['path'],
        'secure' => $p['secure'], 'httponly' => true, 'samesite' => 'Lax']);
    session_destroy();
}

/** Write to cmerti-site's audit trail (diary_audit_log), if it exists. Never throws. */
function dm_audit(string $action, string $subject = '', ?int $id = null, string $detail = ''): void
{
    try {
        $me = session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['dm_uid'])
            ? dm_one('SELECT id, username FROM ' . dm_diary_table('users') . ' WHERE id = ?', [(int) $_SESSION['dm_uid']])
            : null;
        dm_run('INSERT INTO ' . dm_diary_table('audit_log')
            . ' (user_id, username, action, subject, subject_id, detail, ip_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
            [$me['id'] ?? null, mb_substr((string) ($me['username'] ?? 'anonymous'), 0, 60),
             mb_substr($action, 0, 40), mb_substr($subject, 0, 60), $id, mb_substr($detail, 0, 400), dm_ip_hash()]);
    } catch (Throwable $e) { /* silent, as in cmerti-site */ }
}

/* ================================================================== CSRF */

function dm_csrf(): string
{
    dm_session_start();
    if (empty($_SESSION['dm_csrf'])) $_SESSION['dm_csrf'] = bin2hex(random_bytes(32));
    return (string) $_SESSION['dm_csrf'];
}

function dm_csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . esc(dm_csrf()) . '">';
}

function dm_check_csrf(): void
{
    dm_session_start();
    $len = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($_POST === [] && $len > 0) {
        dm_fatal('What you sent was larger than the server accepts in one request. Nothing was saved. Attach a smaller photo.', 413);
    }
    $sent = (string) ($_POST['_token'] ?? '');
    $have = (string) ($_SESSION['dm_csrf'] ?? '');
    if ($have === '' || !hash_equals($have, $sent)) {
        dm_fatal('That form expired or came from somewhere unexpected. Please go back and try again.', 419);
    }
}

/* ================================================================= flash */

function dm_flash(string $msg, string $kind = 'ok'): void
{
    dm_session_start();
    $_SESSION['dm_flash'] = ['msg' => $msg, 'kind' => $kind];
}

function dm_take_flash(): ?array
{
    dm_session_boot();
    if (session_status() !== PHP_SESSION_ACTIVE || empty($_SESSION['dm_flash'])) return null;
    $f = $_SESSION['dm_flash'];
    unset($_SESSION['dm_flash']);
    return $f;
}

/* ================================================================= errors */

function dm_fatal(string $message, int $code = 500): void
{
    http_response_code($code);
    $TITLE = 'Not possible';
    $ME    = null;
    require __DIR__ . '/app-header.php';
    echo '<div class="dnote" role="alert"><strong>' . esc($TITLE) . '.</strong> ' . esc($message)
       . ' <a href="' . esc(dm_url()) . '">Back to Disease Monitoring</a>.</div>';
    require __DIR__ . '/app-footer.php';
    exit;
}
