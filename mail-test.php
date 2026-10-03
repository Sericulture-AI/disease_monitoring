<?php
/**
 * E-mail check (administrators) — shows whether sending is set up and sends a
 * test message with a sample PDF, so the SMTP settings can be proved before
 * staff rely on them.
 */
require_once __DIR__ . '/includes/report.php';
$ME = dm_require_login();
if (!dm_is_admin($ME)) dm_fatal('That page is for administrators only.', 403);

$result = null;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    dm_check_csrf();
    $to = trim((string) ($_POST['to'] ?? ''));
    $p  = new DmPdf();
    $p->addPage();
    $p->font(true, 16); $p->text(50, 80, 'Disease Monitoring - test PDF');
    $p->font(false, 11); $p->text(50, 104, 'If you can read this, PDF attachments are being delivered. Sent ' . date('d M Y H:i'));
    $err = dm_mail_send($to, '[Disease Monitoring] Test message', "This is a test from the Disease Monitoring module.\r\nA sample PDF is attached.",
                        [['test.pdf', 'application/pdf', $p->output('Test')]]);
    $result = $err === '' ? ['ok', 'Sent to ' . $to . '. Check the inbox (and the spam folder).'] : ['bad', 'Not sent: ' . $err];
    dm_audit('mail_test', 'disease_mail', null, $to . ' — ' . ($err === '' ? 'sent' : $err));
}

$m = (array) dm_setting('mail', []);
$s = (array) ($m['smtp'] ?? []);
$TITLE = 'E-mail Check';
require __DIR__ . '/includes/app-header.php';
?>
<article class="diary diary--narrow">
  <?php if ($result): ?>
    <p class="diary-flash diary-flash--<?php echo $result[0]; ?>" role="status"><?php echo esc($result[1]); ?></p>
  <?php endif; ?>

  <dl class="dm-kv">
    <div><dt>Sender</dt><dd><?php echo esc((string) ($m['from_email'] ?? '') ?: ((string) ($s['username'] ?? '') ?: 'not set')); ?></dd></div>
    <div><dt>Method</dt><dd><?php echo trim((string) ($s['host'] ?? '')) !== '' ? 'SMTP ' . esc($s['host'] . ':' . ($s['port'] ?? '') . ' ' . ($s['secure'] ?? '')) : 'PHP mail() (SMTP not set)'; ?></dd></div>
    <div><dt>Always notified</dt><dd><?php echo esc(implode(', ', (array) ($m['always_notify'] ?? [])) ?: 'nobody'); ?></dd></div>
    <div><dt>Copy to recorder</dt><dd><?php echo !empty($m['copy_to_recorder']) ? 'yes' : 'no'; ?></dd></div>
    <div><dt>OpenSSL</dt><dd><?php echo extension_loaded('openssl') ? 'available' : 'MISSING — enable extension=openssl in php.ini'; ?></dd></div>
  </dl>

  <form method="post" class="diary-form" style="margin-top:20px">
    <?php echo dm_csrf_field(); ?>
    <div class="diary-field">
      <label for="to">Send a test message to</label>
      <input type="email" id="to" name="to" required value="<?php echo esc((string) ($ME['email'] ?? '')); ?>">
    </div>
    <button class="btn btn--solid" type="submit">Send test</button>
    <p class="diary-hint">Settings are in <code>disease-monitoring/config.php</code> → <code>mail</code>.</p>
  </form>
</article>
<?php require __DIR__ . '/includes/app-footer.php';
