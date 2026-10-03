<?php
/**
 * One record — everything that was entered, the grading, the alerts, who the
 * PDF was sent to and whether it arrived, and buttons to open the PDF or send
 * it again (to the same people, or to more).
 */
require_once __DIR__ . '/includes/report.php';
$ME  = dm_require_login();
$id  = (int) ($_GET['id'] ?? 0);
$rec = dm_record_or_404($id, $ME);

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    dm_check_csrf();
    $do = (string) ($_POST['do'] ?? '');

    if ($do === 'add_emails') {
        $new = [];
        foreach (preg_split('/[\s,;]+/', (string) ($_POST['emails'] ?? '')) ?: [] as $e) {
            $e = trim($e);
            if ($e === '') continue;
            if (!filter_var($e, FILTER_VALIDATE_EMAIL)) { dm_flash('Not a valid e-mail address: ' . $e, 'bad'); header('Location: ' . dm_url('record.php', ['id' => $id])); exit; }
            $new[strtolower($e)] = $e;
        }
        if (!$new || count($new) > 20) { dm_flash('Enter between one and twenty addresses.', 'bad'); header('Location: ' . dm_url('record.php', ['id' => $id])); exit; }
        $have = [];
        foreach (preg_split('/[\s,;]+/', (string) $rec['notify_emails']) ?: [] as $e) if ($e !== '') $have[strtolower($e)] = $e;
        dm_run('UPDATE ' . dm_table('records') . ' SET notify_emails = ? WHERE id = ?', [implode(', ', $have + $new), $id]);
        $res = dm_notify($rec, (int) $ME['id'], array_values($new));      // only the new addresses
    } else {
        $res = dm_notify($rec, (int) $ME['id']);
    }

    if ($res['sent'] + $res['failed'] === 0) dm_flash('PDF regenerated. There is no address to send it to.', 'bad');
    elseif ($res['failed'] === 0)           dm_flash('PDF e-mailed to ' . $res['sent'] . ' address' . ($res['sent'] === 1 ? '' : 'es') . '.');
    else                                    dm_flash('Sent ' . $res['sent'] . ', failed ' . $res['failed'] . ': ' . $res['errors'][0], 'bad');
    header('Location: ' . dm_url('record.php', ['id' => $id]));
    exit;
}

$find  = dm_findings($id);
$notes = dm_notifications($id);
$D     = dm_diseases();
$flash = dm_take_flash();
dm_audit('view_record', 'disease_record', $id, (string) $rec['ref_no']);

$TITLE = esc((string) $rec['ref_no']) . ' &mdash; ' . esc(ucfirst((string) $rec['crop'])) . ', ' . esc((string) $rec['district']);
require __DIR__ . '/includes/app-header.php';

$kv = function (array $pairs) {
    echo '<dl class="dm-kv">';
    foreach ($pairs as [$k, $v]) {
        if ($v === null || $v === '') continue;
        echo '<div><dt>' . esc($k) . '</dt><dd>' . esc((string) $v) . '</dd></div>';
    }
    echo '</dl>';
};
?>

<article class="diary">
  <?php if ($flash): ?>
    <p class="diary-flash diary-flash--<?php echo $flash['kind'] === 'ok' ? 'ok' : 'bad'; ?>" role="status"><?php echo esc($flash['msg']); ?></p>
  <?php endif; ?>

  <div class="dm-actions">
    <a class="btn btn--solid" href="<?php echo esc(dm_url('pdf.php', ['id' => $id])); ?>" target="_blank" rel="noopener">Open PDF report</a>
    <a class="btn" href="<?php echo esc(dm_url('pdf.php', ['id' => $id, 'dl' => 1])); ?>">Download PDF</a>
    <form method="post" data-confirm="Send the PDF again to every address on this record?">
      <?php echo dm_csrf_field(); ?><input type="hidden" name="do" value="resend">
      <button class="btn" type="submit">Resend e-mail</button>
    </form>
    <a class="btn" href="<?php echo esc(dm_url('record-new.php')); ?>">+ New record</a>
  </div>

  <p>Overall: <span class="sev sev--<?php echo esc((string) $rec['overall']); ?>"><?php echo esc(dm_severity_label((string) $rec['overall'])); ?></span>
     &nbsp; Recorded by <?php echo esc((string) $rec['recorder_name']); ?> on <?php echo esc(date('d M Y, H:i', strtotime((string) $rec['created_at']))); ?></p>

  <?php if (trim((string) $rec['flags']) !== ''): ?>
    <ul class="dm-alerts" role="alert">
      <?php foreach (explode("\n", (string) $rec['flags']) as $fl): ?><li><?php echo esc($fl); ?></li><?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <section class="dsection">
    <h2 class="dsection__h">Findings</h2>
    <?php if (!$find): ?>
      <p>No disease or pest recorded on this visit.</p>
    <?php else: ?>
      <div class="dm-wrap"><table class="dm-table">
        <thead><tr><th>Disease / pest</th><th class="num">Incidence</th><th>Severity</th><th>Recommended management</th></tr></thead>
        <tbody>
          <?php foreach ($find as $x): ?>
            <tr><td><strong><?php echo esc(dm_disease_label($x['disease_key'])); ?></strong></td>
                <td class="num"><?php echo dm_fmt_num($x['incidence_pct'], 2); ?> %</td>
                <td><span class="sev sev--<?php echo esc($x['severity']); ?>"><?php echo esc(dm_severity_label($x['severity'])); ?></span></td>
                <td><?php echo esc($D[$x['disease_key']][3] ?? ''); ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
    <?php endif; ?>
  </section>

  <section class="dsection">
    <h2 class="dsection__h">Visit</h2>
    <?php $kv([
      ['Date observed', date('d M Y', strtotime((string) $rec['obs_date']))],
      ['Crop', ucfirst((string) $rec['crop'])], ['Crop / season', $rec['crop_season']],
      ['Site type', $rec['site_type']], ['Stage', $rec['stage']], ['Host plant', $rec['host_plant']],
      ['DFLs', $rec['dfls']], ['Sample examined', $rec['sample_size']],
      ['Mother moths examined', $rec['mme_examined']], ['Moths positive', $rec['mme_positive']],
    ]); ?>
  </section>

  <section class="dsection">
    <h2 class="dsection__h">Location</h2>
    <?php $kv([
      ['State', $rec['state']], ['District', $rec['district']], ['Block', $rec['block']], ['Village', $rec['village']],
      ['Farm / unit', $rec['site_name']], ['Farmer', $rec['farmer_name']], ['Farmer phone', $rec['farmer_phone']],
      ['GPS', $rec['latitude'] !== null ? $rec['latitude'] . ', ' . $rec['longitude'] : ''],
    ]); ?>
  </section>

  <section class="dsection">
    <h2 class="dsection__h">Weather</h2>
    <?php $kv([
      ['Temperature (°C)', ($rec['temp_min'] !== null || $rec['temp_max'] !== null) ? dm_fmt_num($rec['temp_min']) . ' – ' . dm_fmt_num($rec['temp_max']) : ''],
      ['Relative humidity (%)', ($rec['rh_min'] !== null || $rec['rh_max'] !== null) ? ($rec['rh_min'] ?? '—') . ' – ' . ($rec['rh_max'] ?? '—') : ''],
      ['Rainfall (mm)', $rec['rainfall_mm'] !== null ? dm_fmt_num($rec['rainfall_mm']) : ''],
    ]); ?>
  </section>

  <?php if ($rec['action_taken'] || $rec['remarks']): ?>
    <section class="dsection">
      <h2 class="dsection__h">Action and remarks</h2>
      <?php if ($rec['action_taken']): ?><p><strong>Action taken / advised:</strong><br><?php echo nl2br(esc((string) $rec['action_taken'])); ?></p><?php endif; ?>
      <?php if ($rec['remarks']): ?><p><strong>Remarks:</strong><br><?php echo nl2br(esc((string) $rec['remarks'])); ?></p><?php endif; ?>
    </section>
  <?php endif; ?>

  <?php if ($rec['photo']): ?>
    <section class="dsection">
      <h2 class="dsection__h">Photo</h2>
      <img class="dm-photo" src="<?php echo esc(dm_url('photo.php', ['id' => $id])); ?>" alt="Photo taken at the visit">
    </section>
  <?php endif; ?>

  <section class="dsection">
    <h2 class="dsection__h">E-mail notifications</h2>
    <?php if (!$notes): ?>
      <p class="diary-hint">No e-mail has been sent for this record.</p>
    <?php else: ?>
      <div class="dm-wrap"><table class="dm-table">
        <thead><tr><th>Address</th><th>Status</th><th>When</th></tr></thead>
        <tbody>
          <?php foreach ($notes as $n): ?>
            <tr><td><?php echo esc($n['email']); ?></td>
                <td><?php echo $n['status'] === 'sent'
                      ? '<span class="sev sev--low">Sent</span>'
                      : '<span class="sev sev--high">Failed</span> <small>' . esc((string) $n['error']) . '</small>'; ?></td>
                <td class="nowrap"><?php echo esc(date('d M Y, H:i', strtotime((string) $n['sent_at']))); ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
    <?php endif; ?>

    <form method="post" class="diary-form diary-form--inline" style="margin-top:14px">
      <?php echo dm_csrf_field(); ?>
      <input type="hidden" name="do" value="add_emails">
      <div class="diary-field diary-field--grow">
        <label for="emails">Send the PDF to more addresses</label>
        <input type="text" id="emails" name="emails" placeholder="name@example.com, another@example.com">
      </div>
      <button class="btn btn--solid" type="submit">Send</button>
    </form>
  </section>
</article>

<?php require __DIR__ . '/includes/app-footer.php';
