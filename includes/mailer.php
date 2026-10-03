<?php
/**
 * =============================================================================
 * Disease Monitoring — sending e-mail with a PDF attached
 * =============================================================================
 *
 * A small SMTP client (SSL on 465, or STARTTLS on 587, with AUTH LOGIN) so the
 * module needs no PHPMailer or Composer. Settings are in config.php → 'mail'.
 * If no SMTP host is set it falls back to PHP's mail(), which on XAMPP only
 * works once sendmail has been configured.
 *
 * Returns '' on success, or the reason it failed — which is logged against
 * the record and shown with a Resend button.
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/app.php';

/** @param array $files [[filename, mime, bytes], ...] */
function dm_mail_send(string $to, string $subject, string $text, array $files = []): string
{
    $m    = (array) dm_setting('mail', []);
    $smtp = (array) ($m['smtp'] ?? []);
    $from = (string) ($m['from_email'] ?? '') ?: (string) ($smtp['username'] ?? '');
    $name = (string) ($m['from_name'] ?? 'Disease Monitoring');

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return 'invalid address';
    if (!filter_var($from, FILTER_VALIDATE_EMAIL)) return 'e-mail is not set up: put a sender address in config.php (mail → from_email)';

    $enc = fn(string $s) => preg_match('/[^\x20-\x7E]/', $s) ? '=?UTF-8?B?' . base64_encode($s) . '?=' : $s;
    $boundary = 'dm_' . bin2hex(random_bytes(12));
    $host = preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['SERVER_NAME'] ?? 'localhost')) ?: 'localhost';

    $headers = [
        'Date: ' . date('r'),
        'From: ' . $enc($name) . ' <' . $from . '>',
        'To: <' . $to . '>',
        'Subject: ' . $enc($subject),
        'Message-ID: <' . bin2hex(random_bytes(10)) . '@' . $host . '>',
        'MIME-Version: 1.0',
        'Content-Type: multipart/mixed; boundary="' . $boundary . '"',
        'X-Auto-Response-Suppress: All',
    ];

    $body  = "--$boundary\r\n"
           . "Content-Type: text/plain; charset=UTF-8\r\n"
           . "Content-Transfer-Encoding: base64\r\n\r\n"
           . chunk_split(base64_encode($text)) . "\r\n";
    foreach ($files as [$fname, $mime, $data]) {
        $fname = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $fname);
        $body .= "--$boundary\r\n"
               . "Content-Type: $mime; name=\"$fname\"\r\n"
               . "Content-Transfer-Encoding: base64\r\n"
               . "Content-Disposition: attachment; filename=\"$fname\"\r\n\r\n"
               . chunk_split(base64_encode((string) $data)) . "\r\n";
    }
    $body .= "--$boundary--\r\n";

    if (trim((string) ($smtp['host'] ?? '')) === '') {
        /* mail() takes To and Subject separately */
        $h = array_values(array_filter($headers, fn($l) => stripos($l, 'To:') !== 0 && stripos($l, 'Subject:') !== 0));
        $ok = @mail($to, $enc($subject), $body, implode("\r\n", $h), '-f' . $from);
        return $ok ? '' : 'e-mail is not set up: fill in the SMTP block in config.php (PHP mail() is not configured on this server)';
    }

    return dm_smtp($smtp, $from, $to, implode("\r\n", $headers) . "\r\n\r\n" . $body);
}

/** One message over SMTP. '' on success. */
function dm_smtp(array $s, string $from, string $to, string $data): string
{
    $host   = (string) $s['host'];
    $port   = (int) ($s['port'] ?? 465);
    $secure = strtolower((string) ($s['secure'] ?? ''));
    $tout   = (int) ($s['timeout'] ?? 20);

    $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'SNI_enabled' => true]]);
    $fp  = @stream_socket_client(($secure === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port,
                                 $eno, $estr, $tout, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) return "cannot connect to $host:$port ($estr)";
    stream_set_timeout($fp, $tout);

    $read = function () use ($fp): array {
        $all = '';
        while (($line = fgets($fp, 1024)) !== false) {
            $all .= $line;
            if (strlen($line) < 4 || $line[3] === ' ') break;
        }
        return [(int) substr($all, 0, 3), trim($all)];
    };
    $cmd = function (string $c, array $ok) use ($fp, $read): string {
        if ($c !== '') fwrite($fp, $c . "\r\n");
        [$code, $msg] = $read();
        return in_array($code, $ok, true) ? '' : ($msg !== '' ? $msg : 'no reply from server');
    };

    $me = preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['SERVER_NAME'] ?? 'localhost')) ?: 'localhost';
    try {
        if ($e = $cmd('', [220]))                 return 'server: ' . $e;
        if ($e = $cmd("EHLO $me", [250]))         return 'EHLO: ' . $e;
        if ($secure === 'tls') {
            if ($e = $cmd('STARTTLS', [220]))     return 'STARTTLS: ' . $e;
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) {
                return 'could not start TLS';
            }
            if ($e = $cmd("EHLO $me", [250]))     return 'EHLO: ' . $e;
        }
        $user = (string) ($s['username'] ?? '');
        if ($user !== '') {
            if ($e = $cmd('AUTH LOGIN', [334]))                              return 'AUTH: ' . $e;
            if ($e = $cmd(base64_encode($user), [334]))                      return 'AUTH user: ' . $e;
            if ($e = $cmd(base64_encode((string) ($s['password'] ?? '')), [235])) return 'login refused: ' . $e;
        }
        if ($e = $cmd("MAIL FROM:<$from>", [250]))     return 'MAIL FROM: ' . $e;
        if ($e = $cmd("RCPT TO:<$to>", [250, 251]))    return 'RCPT TO: ' . $e;
        if ($e = $cmd('DATA', [354]))                  return 'DATA: ' . $e;

        /* dot-stuffing: a line that starts with "." gets a second one */
        $data = preg_replace('/^\./m', '..', str_replace(["\r\n", "\r"], ["\n", "\n"], $data));
        $data = str_replace("\n", "\r\n", $data);
        if ($e = $cmd($data . "\r\n.", [250]))         return 'message refused: ' . $e;
        $cmd('QUIT', [221]);
        return '';
    } finally {
        fclose($fp);
    }
}
