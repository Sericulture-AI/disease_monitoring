<?php
/**
 * Records — filter, page through and export. ?export=csv downloads the
 * filtered set (one row per finding) for a spreadsheet.
 */
require_once __DIR__ . '/includes/dm-data.php';
$ME = dm_require_login();
$admin = dm_is_admin($ME);

[$f, $where, $args] = dm_filters($ME);
$R = dm_table('records');

/* ------------------------------------------------------------ CSV export */
if (($_GET['export'] ?? '') === 'csv') {
    $rows = dm_all("SELECT r.*, x.disease_key, x.incidence_pct, x.severity
                    FROM `$R` r LEFT JOIN " . dm_table('findings') . " x ON x.record_id = r.id
                    WHERE $where ORDER BY r.obs_date DESC, r.id DESC, x.incidence_pct DESC", $args);
    dm_audit('export', 'disease_records', null, count($rows) . ' rows');
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="disease-monitoring-' . date('Ymd-His') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");                          // so Excel reads UTF-8
    $cols = ['ref_no','obs_date','crop','crop_season','site_type','stage','state','district','block','village',
             'site_name','farmer_name','farmer_phone','latitude','longitude','host_plant','dfls','sample_size',
             'mme_examined','mme_positive','temp_min','temp_max','rh_min','rh_max','rainfall_mm','overall',
             'recorder_name','created_at'];
    fputcsv($out, array_merge($cols, ['disease', 'incidence_pct', 'severity']));
    foreach ($rows as $r) {
        $line = [];
        foreach ($cols as $c) {
            $v = (string) ($r[$c] ?? '');
            $line[] = preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v;   // no formulas in Excel
        }
        $line[] = $r['disease_key'] ? dm_disease_label((string) $r['disease_key']) : '';
        $line[] = $r['incidence_pct'] ?? '';
        $line[] = $r['severity'] ?? '';
        fputcsv($out, $line);
    }
    fclose($out);
    exit;
}

/* ------------------------------------------------------------ the list */
$per   = 25;
$page  = max(1, (int) ($_GET['p'] ?? 1));
$total = (int) dm_value("SELECT COUNT(*) FROM `$R` r WHERE $where", $args);
$pages = max(1, (int) ceil($total / $per));
$rows  = dm_all("SELECT r.* FROM `$R` r WHERE $where ORDER BY r.obs_date DESC, r.id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $args);
$qs    = array_filter($f, fn($v) => $v !== '');

$flash = dm_take_flash();
$TITLE = $admin ? 'Monitoring Records' : 'My Monitoring Records';
require __DIR__ . '/includes/app-header.php';
$D = dm_diseases();
?>

<article class="diary">
  <?php if ($flash): ?>
    <p class="diary-flash diary-flash--<?php echo $flash['kind'] === 'ok' ? 'ok' : 'bad'; ?>" role="status"><?php echo esc($flash['msg']); ?></p>
  <?php endif; ?>

  <form method="get" class="dm-filter">
    <div class="diary-field"><label for="from">From</label><input type="date" id="from" name="from" value="<?php echo esc($f['from']); ?>"></div>
    <div class="diary-field"><label for="to">To</label><input type="date" id="to" name="to" value="<?php echo esc($f['to']); ?>"></div>
    <div class="diary-field"><label for="crop">Crop</label>
      <select id="crop" name="crop"><option value="">All</option>
        <option value="muga"<?php echo $f['crop'] === 'muga' ? ' selected' : ''; ?>>Muga</option>
        <option value="eri"<?php echo $f['crop'] === 'eri' ? ' selected' : ''; ?>>Eri</option></select></div>
    <div class="diary-field"><label for="district">District</label><input type="text" id="district" name="district" value="<?php echo esc($f['district']); ?>"></div>
    <div class="diary-field"><label for="disease">Disease</label>
      <select id="disease" name="disease"><option value="">Any</option>
        <?php foreach ($D as $k => $d): ?><option value="<?php echo $k; ?>"<?php echo $f['disease'] === $k ? ' selected' : ''; ?>><?php echo esc(ucfirst($d[2]) . ' — ' . $d[0]); ?></option><?php endforeach; ?>
      </select></div>
    <div class="diary-field"><label for="overall">Severity</label>
      <select id="overall" name="overall"><option value="">Any</option>
        <?php foreach (['high','moderate','low','none'] as $s): ?><option value="<?php echo $s; ?>"<?php echo $f['overall'] === $s ? ' selected' : ''; ?>><?php echo dm_severity_label($s); ?></option><?php endforeach; ?>
      </select></div>
    <div class="diary-field"><label for="q">Search</label><input type="search" id="q" name="q" placeholder="Ref., village, farmer…" value="<?php echo esc($f['q']); ?>"></div>
    <?php if ($admin): ?>
      <div class="diary-field"><label for="mine">Whose</label>
        <select id="mine" name="mine"><option value="">All staff</option><option value="1"<?php echo $f['mine'] === '1' ? ' selected' : ''; ?>>Mine only</option></select></div>
    <?php endif; ?>
    <div class="diary-field"><button class="btn btn--solid" type="submit">Filter</button></div>
  </form>

  <div class="dm-actions">
    <a class="btn btn--solid" href="<?php echo esc(dm_url('record-new.php')); ?>">+ New record</a>
    <a class="btn" href="<?php echo esc(dm_url('records.php', $qs + ['export' => 'csv'])); ?>">Download spreadsheet (CSV)</a>
    <span class="diary-hint" style="align-self:center"><?php echo number_format($total); ?> record<?php echo $total === 1 ? '' : 's'; ?></span>
  </div>

  <?php if (!$rows): ?>
    <p class="diary-hint">No records match.</p>
  <?php else: ?>
    <?php require __DIR__ . '/includes/_list.php'; ?>
    <?php if ($pages > 1): ?>
      <nav class="diary-pager" aria-label="Pages" style="margin-top:16px">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <?php if ($i === $page): ?><strong aria-current="page"><?php echo $i; ?></strong>
          <?php else: ?><a href="<?php echo esc(dm_url('records.php', $qs + ['p' => $i])); ?>"><?php echo $i; ?></a><?php endif; ?>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</article>

<?php require __DIR__ . '/includes/app-footer.php';
