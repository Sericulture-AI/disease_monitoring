<?php
/**
 * New Record — one monitoring visit: where, what stage, what was found, the
 * weather, and who should be told. Saving grades it, makes the PDF and
 * e-mails it.
 */
require_once __DIR__ . '/includes/report.php';
$ME = dm_require_login();

$C   = dm_choices();
$D   = dm_diseases();
$err = [];
$v   = [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    dm_check_csrf();
    $v = $_POST;
    [$rec, $findings, $err] = dm_parse_form($_POST);

    /* photo (optional) */
    $photo = null;
    $up = $_FILES['photo'] ?? null;
    if (!$err && $up && ($up['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $max = (int) dm_setting('photo_max_mb', 5) * 1048576;
        if ($up['error'] !== UPLOAD_ERR_OK)          $err['photo'] = 'The photo did not upload. Try a smaller one.';
        elseif ($up['size'] > $max)                  $err['photo'] = 'The photo is larger than ' . dm_setting('photo_max_mb', 5) . ' MB.';
        else {
            $type = @getimagesize($up['tmp_name'])[2] ?? 0;
            $ext  = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png'][$type] ?? null;
            if (!$ext) $err['photo'] = 'Only JPEG or PNG photos. (JPEG photos are also printed in the PDF.)';
            else {
                $photo = date('Ymd-His') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
                if (!move_uploaded_file($up['tmp_name'], dm_storage('photos') . '/' . $photo)) {
                    $err['photo'] = 'The photo could not be stored.'; $photo = null;
                }
            }
        }
    }

    if (!$err) {
        $id  = dm_save($rec, $findings, $ME, $photo);
        $r   = dm_record($id);
        dm_audit('dm_record', 'disease_record', $id, (string) $r['ref_no']);
        try {
            $res = dm_notify($r, (int) $ME['id']);
            if ($res['sent'] + $res['failed'] === 0) {
                dm_flash("Record {$r['ref_no']} saved and the PDF made. No e-mail address was given, so nothing was sent.");
            } elseif ($res['failed'] === 0) {
                dm_flash("Record {$r['ref_no']} saved. PDF e-mailed to {$res['sent']} address" . ($res['sent'] === 1 ? '' : 'es') . '.');
            } else {
                dm_flash("Record {$r['ref_no']} saved, but the e-mail failed for {$res['failed']} address(es): "
                       . $res['errors'][0], 'bad');
            }
        } catch (Throwable $e) {
            dm_flash("Record {$r['ref_no']} saved, but the PDF / e-mail step failed: " . $e->getMessage(), 'bad');
        }
        header('Location: ' . dm_url('record.php', ['id' => $id]));
        exit;
    }
}

$val = fn(string $k, $d = '') => esc((string) ($v[$k] ?? $d));
$sel = fn(string $k, string $opt, $d = '') => ((string) ($v[$k] ?? $d)) === $opt ? ' selected' : '';
$bad = fn(string $k) => isset($err[$k]) ? ' has-err' : '';
$msg = fn(string $k) => isset($err[$k]) ? '<p class="dm-err">' . esc($err[$k]) . '</p>' : '';
$crop = (string) ($v['crop'] ?? 'muga');

$TITLE = 'New Monitoring Record';
require __DIR__ . '/includes/app-header.php';
?>

<article class="diary">

  <?php if ($err): ?>
    <div class="diary-flash diary-flash--bad" role="alert">
      <strong>Nothing was saved yet.</strong> Please correct the marked field<?php echo count($err) > 1 ? 's' : ''; ?>.
      <ul><?php foreach ($err as $e) echo '<li>' . esc($e) . '</li>'; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="dm-form" novalidate id="dmForm">
    <?php echo dm_csrf_field(); ?>

    <fieldset class="dm-fieldset">
      <legend>1. Visit</legend>
      <div class="diary-grid">
        <div class="diary-field<?php echo $bad('obs_date'); ?>">
          <label for="obs_date">Date observed <span class="req">*</span></label>
          <input type="date" id="obs_date" name="obs_date" required max="<?php echo date('Y-m-d'); ?>"
                 value="<?php echo $val('obs_date', date('Y-m-d')); ?>"><?php echo $msg('obs_date'); ?>
        </div>
        <div class="diary-field<?php echo $bad('crop'); ?>">
          <label for="crop">Crop <span class="req">*</span></label>
          <select id="crop" name="crop" required>
            <?php foreach ($C['crop'] as $k => $l): ?>
              <option value="<?php echo $k; ?>"<?php echo $sel('crop', $k, 'muga'); ?>><?php echo $l; ?></option>
            <?php endforeach; ?>
          </select><?php echo $msg('crop'); ?>
        </div>
        <div class="diary-field">
          <label for="crop_season">Crop / season</label>
          <select id="crop_season" name="crop_season">
            <option value="">—</option>
            <?php foreach ($C['season'] as $ck => $list): foreach ($list as $o): ?>
              <option value="<?php echo esc($o); ?>" data-crop="<?php echo $ck; ?>"<?php echo $sel('crop_season', $o); ?>><?php echo esc($o); ?></option>
            <?php endforeach; endforeach; ?>
          </select>
        </div>
        <div class="diary-field<?php echo $bad('site_type'); ?>">
          <label for="site_type">Site type <span class="req">*</span></label>
          <select id="site_type" name="site_type" required>
            <option value="">— choose —</option>
            <?php foreach ($C['site_type'] as $o): ?>
              <option<?php echo $sel('site_type', $o); ?>><?php echo esc($o); ?></option>
            <?php endforeach; ?>
          </select><?php echo $msg('site_type'); ?>
        </div>
        <div class="diary-field<?php echo $bad('stage'); ?>">
          <label for="stage">Stage observed <span class="req">*</span></label>
          <select id="stage" name="stage" required>
            <option value="">— choose —</option>
            <?php foreach ($C['stage'] as $o): ?>
              <option<?php echo $sel('stage', $o); ?>><?php echo esc($o); ?></option>
            <?php endforeach; ?>
          </select><?php echo $msg('stage'); ?>
        </div>
        <div class="diary-field">
          <label for="host_plant">Host plant</label>
          <select id="host_plant" name="host_plant">
            <option value="">—</option>
            <?php foreach ($C['host'] as $o): ?>
              <option<?php echo $sel('host_plant', $o); ?>><?php echo esc($o); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="diary-field<?php echo $bad('dfls'); ?>">
          <label for="dfls">DFLs brushed / in lot</label>
          <input type="number" id="dfls" name="dfls" min="0" step="1" value="<?php echo $val('dfls'); ?>"><?php echo $msg('dfls'); ?>
        </div>
        <div class="diary-field<?php echo $bad('sample_size'); ?>">
          <label for="sample_size">Larvae / plants examined</label>
          <input type="number" id="sample_size" name="sample_size" min="0" step="1" value="<?php echo $val('sample_size'); ?>"><?php echo $msg('sample_size'); ?>
        </div>
      </div>
    </fieldset>

    <fieldset class="dm-fieldset">
      <legend>2. Location</legend>
      <div class="diary-grid">
        <div class="diary-field<?php echo $bad('state'); ?>">
          <label for="state">State <span class="req">*</span></label>
          <select id="state" name="state" required>
            <?php foreach ($C['state'] as $o): ?>
              <option<?php echo $sel('state', $o, 'Assam'); ?>><?php echo esc($o); ?></option>
            <?php endforeach; ?>
          </select><?php echo $msg('state'); ?>
        </div>
        <div class="diary-field<?php echo $bad('district'); ?>">
          <label for="district">District <span class="req">*</span></label>
          <input type="text" id="district" name="district" maxlength="80" required value="<?php echo $val('district'); ?>"><?php echo $msg('district'); ?>
        </div>
        <div class="diary-field">
          <label for="block">Block</label>
          <input type="text" id="block" name="block" maxlength="80" value="<?php echo $val('block'); ?>">
        </div>
        <div class="diary-field">
          <label for="village">Village</label>
          <input type="text" id="village" name="village" maxlength="120" value="<?php echo $val('village'); ?>">
        </div>
        <div class="diary-field">
          <label for="site_name">Farm / unit / centre name</label>
          <input type="text" id="site_name" name="site_name" maxlength="160" value="<?php echo $val('site_name'); ?>">
        </div>
        <div class="diary-field">
          <label for="farmer_name">Farmer / rearer</label>
          <input type="text" id="farmer_name" name="farmer_name" maxlength="120" value="<?php echo $val('farmer_name'); ?>">
        </div>
        <div class="diary-field">
          <label for="farmer_phone">Farmer phone</label>
          <input type="tel" id="farmer_phone" name="farmer_phone" maxlength="20" value="<?php echo $val('farmer_phone'); ?>">
        </div>
        <div class="diary-field<?php echo $bad('latitude'); ?>">
          <label for="latitude">Latitude</label>
          <input type="number" id="latitude" name="latitude" step="0.000001" min="-90" max="90" value="<?php echo $val('latitude'); ?>"><?php echo $msg('latitude'); ?>
        </div>
        <div class="diary-field<?php echo $bad('longitude'); ?>">
          <label for="longitude">Longitude</label>
          <input type="number" id="longitude" name="longitude" step="0.000001" min="-180" max="180" value="<?php echo $val('longitude'); ?>"><?php echo $msg('longitude'); ?>
          <button type="button" class="btn" id="gpsBtn" hidden>Use my location</button>
        </div>
      </div>
    </fieldset>

    <fieldset class="dm-fieldset">
      <legend>3. Findings</legend>
      <p class="diary-hint">Enter the incidence (% of larvae or plants affected) for each disease or pest
        seen. Leave blank if not seen. Severity is graded automatically:
        under <?php echo (float) dm_setting('severity.moderate_from', 5); ?> % low,
        <?php echo (float) dm_setting('severity.moderate_from', 5); ?>&ndash;<?php echo (float) dm_setting('severity.high_from', 15); ?> % moderate,
        above <?php echo (float) dm_setting('severity.high_from', 15); ?> % high; pebrine at any level is high.</p>
      <div class="dm-wrap">
        <table class="dm-findings">
          <thead><tr><th>Disease / pest</th><th>Incidence %</th><th>Recommended management</th></tr></thead>
          <tbody>
            <?php
              $groups = ['silkworm' => 'Silkworm diseases', 'pest' => 'Pests &amp; predators', 'host' => 'Host plant diseases &amp; pests'];
              foreach (['muga', 'eri'] as $cr):
                foreach ($groups as $g => $gl):
                  $rows = array_filter($D, fn($d) => $d[1] === $g && $d[2] === $cr);
                  if (!$rows) continue; ?>
              <tr class="grp<?php echo $cr !== $crop ? ' is-other' : ''; ?>" data-crop="<?php echo $cr; ?>">
                <td colspan="3"><?php echo ucfirst($cr) . ' &mdash; ' . $gl; ?></td></tr>
              <?php foreach ($rows as $k => $d): ?>
              <tr class="<?php echo $cr !== $crop ? 'is-other' : ''; ?><?php echo isset($err['inc_' . $k]) ? ' has-err' : ''; ?>" data-crop="<?php echo $cr; ?>">
                <td><label for="inc_<?php echo $k; ?>"><?php echo esc($d[0]); ?></label><?php echo $msg('inc_' . $k); ?></td>
                <td class="inc"><input type="number" id="inc_<?php echo $k; ?>" name="inc[<?php echo $k; ?>]"
                     min="0" max="100" step="0.01" inputmode="decimal" placeholder="—"
                     value="<?php echo esc((string) ($v['inc'][$k] ?? '')); ?>"></td>
                <td class="adv"><?php echo esc($d[3]); ?></td>
              </tr>
              <?php endforeach; endforeach; endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="diary-grid" style="margin-top:16px">
        <div class="diary-field<?php echo $bad('mme_examined'); ?>">
          <label for="mme_examined">Mother moths examined (pebrine)</label>
          <input type="number" id="mme_examined" name="mme_examined" min="0" step="1" value="<?php echo $val('mme_examined'); ?>"><?php echo $msg('mme_examined'); ?>
        </div>
        <div class="diary-field<?php echo $bad('mme_positive'); ?>">
          <label for="mme_positive">Moths positive</label>
          <input type="number" id="mme_positive" name="mme_positive" min="0" step="1" value="<?php echo $val('mme_positive'); ?>"><?php echo $msg('mme_positive'); ?>
        </div>
      </div>
    </fieldset>

    <fieldset class="dm-fieldset">
      <legend>4. Weather at the site</legend>
      <div class="diary-grid">
        <?php foreach ([['temp_min','Min temp (°C)','0.1'],['temp_max','Max temp (°C)','0.1'],['rh_min','Min RH (%)','1'],['rh_max','Max RH (%)','1'],['rainfall_mm','Rainfall (mm)','0.1']] as [$k,$l,$st]): ?>
          <div class="diary-field<?php echo $bad($k); ?>">
            <label for="<?php echo $k; ?>"><?php echo $l; ?></label>
            <input type="number" id="<?php echo $k; ?>" name="<?php echo $k; ?>" step="<?php echo $st; ?>" value="<?php echo $val($k); ?>"><?php echo $msg($k); ?>
          </div>
        <?php endforeach; ?>
      </div>
    </fieldset>

    <fieldset class="dm-fieldset">
      <legend>5. Action and remarks</legend>
      <div class="diary-grid">
        <div class="diary-field diary-field--wide">
          <label for="action_taken">Action taken / advised on the spot</label>
          <textarea id="action_taken" name="action_taken" maxlength="4000"><?php echo $val('action_taken'); ?></textarea>
        </div>
        <div class="diary-field diary-field--wide">
          <label for="remarks">Remarks</label>
          <textarea id="remarks" name="remarks" maxlength="4000"><?php echo $val('remarks'); ?></textarea>
        </div>
        <div class="diary-field<?php echo $bad('photo'); ?>">
          <label for="photo">Photo (JPEG / PNG, up to <?php echo (int) dm_setting('photo_max_mb', 5); ?> MB)</label>
          <input type="file" id="photo" name="photo" accept="image/jpeg,image/png"><?php echo $msg('photo'); ?>
        </div>
      </div>
    </fieldset>

    <fieldset class="dm-fieldset">
      <legend>6. Send the PDF report to</legend>
      <div class="diary-field diary-field--wide<?php echo $bad('notify_emails'); ?>">
        <label for="notify_emails">E-mail addresses</label>
        <input type="text" id="notify_emails" name="notify_emails" maxlength="2000"
               placeholder="name@example.com, another@example.com"
               value="<?php echo $val('notify_emails'); ?>"><?php echo $msg('notify_emails'); ?>
        <p class="diary-hint">Separate with commas. Each address receives the PDF report as soon as
          the record is saved.
          <?php
            $extra = array_filter((array) dm_setting('mail.always_notify', []));
            if (dm_setting('mail.copy_to_recorder', true) && filter_var((string) ($ME['email'] ?? ''), FILTER_VALIDATE_EMAIL)) $extra[] = 'you (' . $ME['email'] . ')';
            if ($extra) echo 'A copy also goes to ' . esc(implode(', ', $extra)) . '.';
          ?></p>
      </div>
    </fieldset>

    <div class="diary-actions">
      <button class="btn btn--solid diary-submit" type="submit">Save, make PDF and send</button>
      <a class="btn" href="<?php echo esc(dm_url('records.php')); ?>">Cancel</a>
    </div>
  </form>
</article>

<?php require __DIR__ . '/includes/app-footer.php';
