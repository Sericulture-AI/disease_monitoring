<?php
/** A table of records. Expects $rows (records) and $admin. */
$__ids = array_map(fn($r) => (int) $r['id'], $rows);
$__f = [];
if ($__ids) {
    foreach (dm_all('SELECT record_id, disease_key, incidence_pct, severity FROM ' . dm_table('findings')
             . ' WHERE record_id IN (' . implode(',', $__ids) . ') ORDER BY incidence_pct DESC') as $x) {
        $__f[(int) $x['record_id']][] = $x;
    }
}
?>
<div class="dm-wrap">
  <table class="dm-table">
    <thead><tr>
      <th>Ref.</th><th>Date</th><th>Crop</th><th>Location</th><th>Findings</th><th>Overall</th>
      <?php if (!empty($admin)): ?><th>Recorded by</th><?php endif; ?>
    </tr></thead>
    <tbody>
      <?php foreach ($rows as $r): $id = (int) $r['id']; ?>
        <tr>
          <td class="nowrap"><a href="<?php echo esc(dm_url('record.php', ['id' => $id])); ?>"><strong><?php echo esc($r['ref_no']); ?></strong></a></td>
          <td class="nowrap"><?php echo esc(date('d M Y', strtotime((string) $r['obs_date']))); ?></td>
          <td><?php echo esc(ucfirst((string) $r['crop'])); ?><br><small><?php echo esc((string) $r['stage']); ?></small></td>
          <td><?php echo esc(implode(', ', array_filter([$r['village'], $r['district']]))); ?><br><small><?php echo esc((string) $r['state']); ?></small></td>
          <td><?php
            if (empty($__f[$id])) echo '<small>none</small>';
            else echo implode('<br>', array_map(fn($x) => esc(dm_disease_label($x['disease_key'])) . ' <small>' . dm_fmt_num($x['incidence_pct'], 2) . ' %</small>', array_slice($__f[$id], 0, 3)))
                    . (count($__f[$id]) > 3 ? '<br><small>+' . (count($__f[$id]) - 3) . ' more</small>' : '');
          ?></td>
          <td><span class="sev sev--<?php echo esc((string) $r['overall']); ?>"><?php echo esc(dm_severity_label((string) $r['overall'])); ?></span></td>
          <?php if (!empty($admin)): ?><td><?php echo esc((string) $r['recorder_name']); ?></td><?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
