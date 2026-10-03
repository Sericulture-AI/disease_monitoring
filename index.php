<?php
/**
 * Dashboard — what has been recorded, where disease is, and what is waiting.
 * Staff see their own records; administrators see everything.
 */
require_once __DIR__ . '/includes/dm-data.php';
$ME = dm_require_login();

$admin = dm_is_admin($ME);
$R = dm_table('records'); $F = dm_table('findings'); $N = dm_table('notifications');
$scope = $admin ? '1=1' : 'r.user_id = ' . (int) $ME['id'];

$since = date('Y-m-d', strtotime('-30 days'));
$tot   = (int) dm_value("SELECT COUNT(*) FROM `$R` r WHERE $scope");
$last  = (int) dm_value("SELECT COUNT(*) FROM `$R` r WHERE $scope AND obs_date >= ?", [$since]);
$high  = (int) dm_value("SELECT COUNT(*) FROM `$R` r WHERE $scope AND obs_date >= ? AND overall = 'high'", [$since]);
$fail  = (int) dm_value("SELECT COUNT(DISTINCT r.id) FROM `$R` r JOIN `$N` n ON n.record_id = r.id
                         WHERE $scope AND n.status = 'failed'
                           AND NOT EXISTS (SELECT 1 FROM `$N` m WHERE m.record_id = r.id AND m.email = n.email AND m.status = 'sent' AND m.id > n.id)");

$byDisease = dm_all("SELECT f.disease_key, COUNT(*) n, ROUND(AVG(f.incidence_pct),1) avg_pct, SUM(f.severity = 'high') hi
                     FROM `$F` f JOIN `$R` r ON r.id = f.record_id
                     WHERE $scope AND r.obs_date >= ? GROUP BY f.disease_key ORDER BY n DESC LIMIT 10", [date('Y-m-d', strtotime('-90 days'))]);
$byDistrict = dm_all("SELECT r.district, COUNT(*) n, SUM(r.overall = 'high') hi
                      FROM `$R` r WHERE $scope AND r.obs_date >= ? GROUP BY r.district ORDER BY hi DESC, n DESC LIMIT 10",
                      [date('Y-m-d', strtotime('-90 days'))]);
$recent = dm_all("SELECT r.* FROM `$R` r WHERE $scope ORDER BY r.obs_date DESC, r.id DESC LIMIT 10");

$flash = dm_take_flash();
$TITLE = 'Disease Monitoring';
require __DIR__ . '/includes/app-header.php';
$maxD = max(1, ...array_map(fn($x) => (int) $x['n'], $byDisease ?: [['n' => 1]]));
?>

<article class="diary">

  <?php if ($flash): ?>
    <p class="diary-flash diary-flash--<?php echo $flash['kind'] === 'ok' ? 'ok' : 'bad'; ?>" role="status"><?php echo esc($flash['msg']); ?></p>
  <?php endif; ?>

  <div class="dm-actions">
    <a class="btn btn--solid" href="<?php echo esc(dm_url('record-new.php')); ?>">+ New monitoring record</a>
    <a class="btn" href="<?php echo esc(dm_url('records.php')); ?>"><?php echo $admin ? 'All records' : 'My records'; ?></a>
  </div>

  <div class="dstats">
    <div class="dstat"><strong><?php echo number_format($tot); ?></strong><span><?php echo $admin ? 'Records, all staff' : 'My records'; ?></span></div>
    <div class="dstat"><strong><?php echo number_format($last); ?></strong><span>Last 30 days</span></div>
    <div class="dstat"><strong><?php echo number_format($high); ?></strong><span>High severity, 30 days</span></div>
    <div class="dstat"><strong><?php echo number_format($fail); ?></strong><span>E-mails not delivered</span></div>
  </div>

  <?php if ($fail > 0): ?>
    <div class="dnote" role="status">
      <strong><?php echo $fail; ?> record<?php echo $fail === 1 ? '' : 's'; ?> could not be e-mailed.</strong>
      Open the record and press <em>Resend</em>. If every send fails, the SMTP block in
      <code>config.php</code> has not been filled in yet.
    </div>
  <?php endif; ?>

  <div class="dm-two">
    <section class="dm-card">
      <h2>Diseases recorded &mdash; last 90 days</h2>
      <?php if (!$byDisease): ?>
        <p class="diary-hint">Nothing recorded yet.</p>
      <?php else: ?>
        <div class="dm-bars">
          <?php foreach ($byDisease as $d): ?>
            <div class="dm-bar" title="<?php echo (int) $d['n']; ?> records, average <?php echo esc((string) $d['avg_pct']); ?> %, <?php echo (int) $d['hi']; ?> high">
              <span><?php echo esc(dm_disease_label($d['disease_key'])); ?></span>
              <span class="dm-bar__track"><span class="dm-bar__fill" style="width:<?php echo round(100 * $d['n'] / $maxD); ?>%"></span></span>
              <span class="dm-bar__n"><?php echo (int) $d['n']; ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="dm-card">
      <h2>Districts &mdash; last 90 days</h2>
      <?php if (!$byDistrict): ?>
        <p class="diary-hint">Nothing recorded yet.</p>
      <?php else: ?>
        <table class="dm-table">
          <thead><tr><th>District</th><th class="num">Records</th><th class="num">High</th></tr></thead>
          <tbody>
            <?php foreach ($byDistrict as $d): ?>
              <tr><td><a href="<?php echo esc(dm_url('records.php', ['district' => $d['district']])); ?>"><?php echo esc($d['district']); ?></a></td>
                  <td class="num"><?php echo (int) $d['n']; ?></td>
                  <td class="num"><?php echo (int) $d['hi'] ? '<span class="sev sev--high">' . (int) $d['hi'] . '</span>' : '0'; ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
  </div>

  <section class="dsection">
    <h2 class="dsection__h">Latest records</h2>
    <?php if (!$recent): ?>
      <p class="diary-hint">No records yet. <a href="<?php echo esc(dm_url('record-new.php')); ?>">Enter the first one</a>.</p>
    <?php else: ?>
      <?php $rows = $recent; require __DIR__ . '/includes/_list.php'; ?>
    <?php endif; ?>
  </section>
</article>

<?php require __DIR__ . '/includes/app-footer.php';
