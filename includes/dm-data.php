<?php
/**
 * =============================================================================
 * Disease Monitoring — what can be recorded, how it is graded, and storage
 * =============================================================================
 *
 * The disease list and every line of advice below are taken from the
 * institute's own pages (Silkworm Diseases, Host Plant Diseases, Insect Pests
 * & Predators, Disease Forewarning) — the page each came from is named
 * against it. Change the advice there and here together.
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/app.php';

/* ============================================================ catalogue */

/**
 * key => [label, group, crop ('muga' | 'eri' | 'both'), advice]
 * group: silkworm | pest | host
 */
function dm_diseases(): array
{
    return [
        /* ---- muga silkworm — Silkworm Diseases page */
        'muga_pebrine'      => ['Pebrine (Nosema sp.)', 'silkworm', 'muga',
            'MME-certified, surface-sterilised DFLs; individual mother moth examination; disinfect appliances with 5 % bleaching powder or 0.02 % NaOCl; dust 9:1 slaked lime : bleaching powder around field and grainage. Reject an infected seed lot.'],
        'muga_muscardine'   => ['Muscardine (white / black / green)', 'silkworm', 'muga',
            'Field disinfection; quality stage-specific leaf; 0.1 % LAHDOI (1 g/L) on plants and soil 7 days before brushing, then every 15 days; sunny plot and nylon net (Jarua).'],
        'muga_aspergillosis'=> ['Aspergillosis', 'silkworm', 'muga',
            'As for muscardine; strict early-age hygiene.'],
        'muga_grasserie'    => ['Grasserie (NPV)', 'silkworm', 'muga',
            'Egg disinfection with 0.2 % NaOCl; remove and destroy infected larvae; lime dusting to cut humidity; bury dead worms and litter.'],
        'muga_bact_flacherie'=> ['Bacterial flacherie', 'silkworm', 'muga',
            'As for grasserie; proper pruning schedule and age-specific leaf.'],
        'muga_inf_flacherie'=> ['Infectious flacherie (IFV / DNV)', 'silkworm', 'muga',
            'As for grasserie; feed healthy leaf only.'],
        'muga_uzi'          => ['Uzi fly (Exorista / Blepharipa)', 'pest', 'muga',
            'Keep nylon net erected and maintained; collect and destroy infested larvae and cocoons; never carry suspect cocoons into the grainage; release Nesolynx thymus around rearing fields. Chemical control is not available for muga.'],
        'muga_predators'    => ['Predators (ants, wasps, stink bug, birds)', 'pest', 'muga',
            'Trunk banding to 2–2.5 ft; clear ant nests and spider nets before brushing; net management.'],

        /* ---- eri silkworm — Silkworm Diseases / Pests pages */
        'eri_pebrine'       => ['Pebrine (N. philosomiae / N. ricini)', 'silkworm', 'eri',
            'Mother moth examination; certified DFLs; egg surface sterilisation; grainage hygiene. At a chawki centre: reject and burn the batch and disinfect the centre immediately.'],
        'eri_muscardine'    => ['Muscardine', 'silkworm', 'eri',
            'Disinfection; bed drying with lime; remove infected larvae.'],
        'eri_grasserie'     => ['Grasserie (SariNPV)', 'silkworm', 'eri',
            'Lime dusting; sanitation; no overcrowding; avoid tender leaf in late age; wash hands after handling infected larvae.'],
        'eri_flacherie'     => ['Flacherie', 'silkworm', 'eri',
            'Leaf quality control; bed hygiene; stocking discipline.'],
        'eri_uzi'           => ['Uzi fly', 'pest', 'eri',
            'Wire mesh or nylon net on doors and windows; self-closing doors; ante-chamber; release Nesolynx thymus; never dispose of litter in the open.'],
        'eri_pests'         => ['Ants / cockroach / rat / lizard', 'pest', 'eri',
            'Ant wells at every rearing stand; repellent barriers; traps or baits; rearing-house hygiene.'],

        /* ---- host plants — Host Plant Diseases / Forewarning pages */
        'som_leaf_spot'     => ['Som — leaf spot', 'host', 'muga',
            'Pruning and pollarding; 0.1 % Dithane / Indofil M-45 (1 g/L) twice at 15-day interval before the Jun–Aug peak; S3 som.'],
        'som_anthracnose'   => ['Som — anthracnose', 'host', 'muga',
            'Pluck infected parts and burn; 0.1 % Bavistin (1 g/L) twice at 15-day interval before the peak.'],
        'som_grey_blight'   => ['Som — grey blight', 'host', 'muga',
            'Timely pruning and pollarding; 0.1 % Bavistin twice at 15-day interval.'],
        'som_red_rust'      => ['Som / soalu — red rust', 'host', 'muga',
            'Prune and pluck infected parts; 1 % Bordeaux mixture.'],
        'soalu_brown_blight'=> ['Soalu — brown blight', 'host', 'muga',
            'Pluck and burn; copper oxychloride (Fytolan / Blitox-50) 3 g/L at 15-day intervals.'],
        'soalu_grey_blight' => ['Soalu — grey blight', 'host', 'muga',
            'Pluck and burn; copper oxychloride 3 g/L as prophylactic.'],
        'stem_borer'        => ['Stem borer', 'host', 'muga',
            '5–15 % extract of neem, dhatura, titabahak or castor; plug holes with cotton soaked in 1.5 % Nuvan and plaster with mud.'],
        'leaf_gall'         => ['Leaf gall', 'host', 'muga',
            'Collect and destroy infested leaves and twigs; above 25 % infestation prune and burn.'],
        'castor_seedling_blight' => ['Castor — seedling blight', 'host', 'eri',
            'Fytolan / Blitox-50 3 g/L at 15-day intervals. Do not feed leaf for 7 days after spraying.'],
        'castor_alternaria' => ['Castor — Alternaria blight', 'host', 'eri',
            'Fytolan / Blitox-50 3 g/L at 15-day intervals. Do not feed leaf for 7 days after spraying.'],
        'castor_cercospora' => ['Castor — Cercospora leaf spot', 'host', 'eri',
            'Dithane Z-78 or Ziram 2 g/L.'],
        'castor_powdery'    => ['Castor — powdery mildew', 'host', 'eri',
            '0.2 % wettable sulphur at 15-day intervals in dry weather. Do not feed leaf for one month after spraying.'],
        'castor_wilt'       => ['Castor — Fusarium wilt', 'host', 'eri',
            'Seed treatment with Thiram 3 g/kg or Carbendazim 2 g/kg; soil drench with Carbendazim.'],
    ];
}

function dm_disease_label(string $key): string
{
    return dm_diseases()[$key][0] ?? $key;
}

/* ============================================================== choices */

function dm_choices(): array
{
    return [
        'crop'   => ['muga' => 'Muga', 'eri' => 'Eri'],
        'season' => [
            'muga' => ['Jarua (Dec–Jan)', 'Chotua (Feb–Mar)', 'Jethua (Apr–May)',
                       'Aherua (Jun–Jul)', 'Bhodia (Aug–Sep)', 'Kotia (Oct–Nov)'],
            'eri'  => ['Spring (Feb–Apr)', 'Summer (May–Jun)', 'Rainy (Jul–Sep)',
                       'Autumn (Oct–Nov)', 'Winter (Dec–Jan)'],
        ],
        'site_type' => ['Farmer’s rearing field', 'Institute farm', 'Grainage',
                        'Chawki rearing centre', 'Eri rearing house', 'Host plantation',
                        'Seed / P4 station', 'Other'],
        'stage'  => ['Egg / DFL', '1st instar', '2nd instar', '3rd instar', '4th instar',
                     '5th instar', 'Spinning / cocoon', 'Pupa', 'Moth / grainage', 'Host plant only'],
        'host'   => ['Som', 'Soalu', 'Dighloti', 'Castor', 'Kesseru', 'Tapioca', 'Borpat', 'Other'],
        'state'  => ['Assam', 'Arunachal Pradesh', 'Manipur', 'Meghalaya', 'Mizoram',
                     'Nagaland', 'Sikkim', 'Tripura', 'Other'],
    ];
}

/* ============================================================== grading */

function dm_grade(string $key, float $pct): string
{
    $zero = (array) dm_setting('severity.zero_tolerance', []);
    if (in_array($key, $zero, true) && $pct > 0) return 'high';
    if ($pct >= (float) dm_setting('severity.high_from', 15))     return 'high';
    if ($pct >= (float) dm_setting('severity.moderate_from', 5))  return 'moderate';
    return 'low';
}

function dm_overall(array $findings): string
{
    $rank = ['none' => 0, 'low' => 1, 'moderate' => 2, 'high' => 3];
    $top  = 'none';
    foreach ($findings as $f) if ($rank[$f['severity']] > $rank[$top]) $top = $f['severity'];
    return $top;
}

function dm_severity_label(string $s): string
{
    return ['none' => 'No disease', 'low' => 'Low', 'moderate' => 'Moderate', 'high' => 'High'][$s] ?? $s;
}

/** Automatic warnings that go on the record and the PDF. */
function dm_flags(array $r, array $findings): array
{
    $out = [];

    $ex = (int) ($r['mme_examined'] ?? 0); $pos = (int) ($r['mme_positive'] ?? 0);
    if ($ex > 0 && $pos > 0) {
        $out[] = sprintf('Mother moth examination: %d of %d moths pebrine-positive (%.1f %%). Do not issue DFLs from this lot.',
                         $pos, $ex, 100 * $pos / $ex);
    }

    $mw = (array) dm_setting('muscardine_weather', []);
    if (($r['crop'] ?? '') === 'muga' && $r['temp_min'] !== null && $r['rh_max'] !== null
        && (float) $r['temp_min'] < (float) ($mw['min_temp_below'] ?? 10)
        && (int) $r['rh_max'] >= (int) ($mw['rh_max_at_least'] ?? 72)) {
        $out[] = sprintf('Muscardine weather: minimum %.1f °C with RH up to %d %% — low temperature with high humidity is the predisposing combination. Apply LAHDOI before brushing and keep the plot sunny.',
                         (float) $r['temp_min'], (int) $r['rh_max']);
    }

    if ($r['rainfall_mm'] !== null && (float) $r['rainfall_mm'] >= 50 && ($r['crop'] ?? '') === 'muga') {
        $out[] = 'Heavy rainfall: muscardine risk has been seen moving into Chotua and early Jethua — read the forewarning calendar against actual rainfall, not the month alone.';
    }

    foreach ($findings as $f) {
        if ($f['severity'] === 'high') {
            $out[] = dm_disease_label($f['disease_key']) . ': high incidence (' . rtrim(rtrim(number_format((float) $f['incidence_pct'], 2), '0'), '.') . ' %). Act now and re-inspect.';
        }
    }
    return $out;
}

/* ============================================================== saving */

/** Clean the submitted form. Returns [record, findings, errors]. */
function dm_parse_form(array $p): array
{
    $err = [];
    $c   = dm_choices();
    $s   = static fn($k, $max = 200) => mb_substr(trim((string) ($p[$k] ?? '')), 0, $max);
    $num = static function ($k, $min, $max, $int = false) use ($p, &$err) {
        $v = trim((string) ($p[$k] ?? ''));
        if ($v === '') return null;
        if (!is_numeric($v) || (float) $v < $min || (float) $v > $max) { $err[$k] = "Must be a number between $min and $max."; return null; }
        return $int ? (int) $v : (float) $v;
    };

    $r = [
        'obs_date'     => $s('obs_date', 10),
        'crop'         => $s('crop', 10),
        'crop_season'  => $s('crop_season', 40),
        'site_type'    => $s('site_type', 40),
        'stage'        => $s('stage', 40),
        'state'        => $s('state', 60),
        'district'     => $s('district', 80),
        'block'        => $s('block', 80),
        'village'      => $s('village', 120),
        'site_name'    => $s('site_name', 160),
        'farmer_name'  => $s('farmer_name', 120),
        'farmer_phone' => preg_replace('/[^0-9+ ]/', '', $s('farmer_phone', 20)),
        'latitude'     => $num('latitude', -90, 90),
        'longitude'    => $num('longitude', -180, 180),
        'host_plant'   => $s('host_plant', 40),
        'dfls'         => $num('dfls', 0, 1000000, true),
        'sample_size'  => $num('sample_size', 0, 1000000, true),
        'mme_examined' => $num('mme_examined', 0, 1000000, true),
        'mme_positive' => $num('mme_positive', 0, 1000000, true),
        'temp_min'     => $num('temp_min', -10, 50),
        'temp_max'     => $num('temp_max', -10, 55),
        'rh_min'       => $num('rh_min', 0, 100, true),
        'rh_max'       => $num('rh_max', 0, 100, true),
        'rainfall_mm'  => $num('rainfall_mm', 0, 2000),
        'action_taken' => $s('action_taken', 4000),
        'remarks'      => $s('remarks', 4000),
        'notify_emails'=> '',
    ];

    $d = DateTime::createFromFormat('Y-m-d', $r['obs_date']);
    if (!$d || $d->format('Y-m-d') !== $r['obs_date'])      $err['obs_date'] = 'Enter the date of observation.';
    elseif ($r['obs_date'] > date('Y-m-d'))                 $err['obs_date'] = 'The date cannot be in the future.';
    if (!isset($c['crop'][$r['crop']]))                     $err['crop'] = 'Choose muga or eri.';
    if ($r['site_type'] === '')                             $err['site_type'] = 'Choose the type of site.';
    if ($r['stage'] === '')                                 $err['stage'] = 'Choose the stage observed.';
    if ($r['state'] === '')                                 $err['state'] = 'Choose the state.';
    if ($r['district'] === '')                              $err['district'] = 'Enter the district.';
    if ($r['mme_positive'] !== null && $r['mme_examined'] !== null && $r['mme_positive'] > $r['mme_examined'])
        $err['mme_positive'] = 'Cannot be more than the number examined.';
    if ($r['temp_min'] !== null && $r['temp_max'] !== null && $r['temp_min'] > $r['temp_max'])
        $err['temp_max'] = 'Maximum is below minimum.';
    if ($r['rh_min'] !== null && $r['rh_max'] !== null && $r['rh_min'] > $r['rh_max'])
        $err['rh_max'] = 'Maximum is below minimum.';

    /* findings: inc[<key>] = incidence %, blank = not observed */
    $findings = [];
    $all = dm_diseases();
    foreach ((array) ($p['inc'] ?? []) as $key => $v) {
        $key = (string) $key; $v = trim((string) $v);
        if ($v === '' || !isset($all[$key])) continue;
        $crop = $all[$key][2];
        if ($crop !== 'both' && $crop !== $r['crop']) continue;          // other crop's rows ignored
        if (!is_numeric($v) || (float) $v < 0 || (float) $v > 100) { $err['inc_' . $key] = 'Incidence must be 0–100 %.'; continue; }
        if ((float) $v == 0.0) continue;
        $findings[] = ['disease_key' => $key, 'incidence_pct' => round((float) $v, 2),
                       'severity' => dm_grade($key, (float) $v)];
    }

    /* MME positives count as a pebrine finding if none was typed. */
    if ($r['mme_examined'] && $r['mme_positive']) {
        $pk = $r['crop'] === 'eri' ? 'eri_pebrine' : 'muga_pebrine';
        if (!array_filter($findings, fn($f) => $f['disease_key'] === $pk)) {
            $pct = round(100 * $r['mme_positive'] / $r['mme_examined'], 2);
            $findings[] = ['disease_key' => $pk, 'incidence_pct' => $pct, 'severity' => dm_grade($pk, $pct)];
        }
    }

    /* e-mails */
    $emails = [];
    foreach (preg_split('/[\s,;]+/', (string) ($p['notify_emails'] ?? '')) ?: [] as $e) {
        $e = trim($e);
        if ($e === '') continue;
        if (!filter_var($e, FILTER_VALIDATE_EMAIL)) { $err['notify_emails'] = 'Not a valid e-mail address: ' . $e; continue; }
        $emails[strtolower($e)] = $e;
    }
    if (count($emails) > 20) $err['notify_emails'] = 'At most 20 addresses.';
    $r['notify_emails'] = implode(', ', $emails);

    return [$r, $findings, $err];
}

/** Next reference number, e.g. DM-2026-0007. */
function dm_next_ref(): string
{
    $y = date('Y');
    $last = (string) dm_value('SELECT ref_no FROM ' . dm_table('records')
        . " WHERE ref_no LIKE ? ORDER BY id DESC LIMIT 1", ["DM-$y-%"], '');
    $n = $last !== '' ? (int) substr($last, -4) + 1 : 1;
    return sprintf('DM-%s-%04d', $y, $n);
}

function dm_save(array $r, array $findings, array $user, ?string $photo): int
{
    $pdo = dm_db();
    $pdo->beginTransaction();
    try {
        $r['ref_no']        = dm_next_ref();
        $r['user_id']       = (int) $user['id'];
        $r['recorder_name'] = dm_name($user);
        $r['overall']       = dm_overall($findings);
        $r['flags']         = implode("\n", dm_flags($r, $findings));
        $r['photo']         = $photo;
        $r['created_at']    = date('Y-m-d H:i:s');

        $cols = array_keys($r);
        dm_run('INSERT INTO ' . dm_table('records') . ' (`' . implode('`,`', $cols) . '`) VALUES ('
               . implode(',', array_fill(0, count($cols), '?')) . ')', array_values($r));
        $id = (int) $pdo->lastInsertId();

        $st = $pdo->prepare('INSERT INTO ' . dm_table('findings')
            . ' (record_id, disease_key, incidence_pct, severity) VALUES (?, ?, ?, ?)');
        foreach ($findings as $f) $st->execute([$id, $f['disease_key'], $f['incidence_pct'], $f['severity']]);

        $pdo->commit();
        return $id;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/* ============================================================= reading */

function dm_record(int $id): ?array
{
    return dm_one('SELECT * FROM ' . dm_table('records') . ' WHERE id = ?', [$id]);
}

function dm_findings(int $id): array
{
    return dm_all('SELECT * FROM ' . dm_table('findings') . ' WHERE record_id = ? ORDER BY incidence_pct DESC', [$id]);
}

function dm_notifications(int $id): array
{
    return dm_all('SELECT * FROM ' . dm_table('notifications') . ' WHERE record_id = ? ORDER BY id DESC', [$id]);
}

/** May this user see this record? Own records, or everything for an admin. */
function dm_can_see(array $rec, array $user): bool
{
    return dm_is_admin($user) || (int) $rec['user_id'] === (int) $user['id'];
}

/** Record or a 404 page. */
function dm_record_or_404(int $id, array $user): array
{
    $rec = $id > 0 ? dm_record($id) : null;
    if (!$rec || !dm_can_see($rec, $user)) dm_fatal('That record does not exist, or it is not yours to open.', 404);
    return $rec;
}

/** Filters from the query string → [where SQL, args]. */
function dm_filters(array $user): array
{
    $f = [
        'from'     => (string) ($_GET['from'] ?? ''),
        'to'       => (string) ($_GET['to'] ?? ''),
        'crop'     => (string) ($_GET['crop'] ?? ''),
        'district' => trim((string) ($_GET['district'] ?? '')),
        'disease'  => (string) ($_GET['disease'] ?? ''),
        'overall'  => (string) ($_GET['overall'] ?? ''),
        'q'        => trim((string) ($_GET['q'] ?? '')),
        'mine'     => dm_is_admin($user) ? (string) ($_GET['mine'] ?? '') : '1',
    ];
    $w = ['1=1']; $a = [];
    $R = 'r.';
    if ($f['mine'] === '1')                                        { $w[] = $R . 'user_id = ?'; $a[] = (int) $user['id']; }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['from']))           { $w[] = $R . 'obs_date >= ?'; $a[] = $f['from']; }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['to']))             { $w[] = $R . 'obs_date <= ?'; $a[] = $f['to']; }
    if (in_array($f['crop'], ['muga', 'eri'], true))               { $w[] = $R . 'crop = ?'; $a[] = $f['crop']; }
    if ($f['district'] !== '')                                     { $w[] = $R . 'district LIKE ?'; $a[] = '%' . $f['district'] . '%'; }
    if (in_array($f['overall'], ['none', 'low', 'moderate', 'high'], true)) { $w[] = $R . 'overall = ?'; $a[] = $f['overall']; }
    if (isset(dm_diseases()[$f['disease']])) {
        $w[] = 'EXISTS (SELECT 1 FROM ' . dm_table('findings') . ' x WHERE x.record_id = r.id AND x.disease_key = ?)';
        $a[] = $f['disease'];
    }
    if ($f['q'] !== '') {
        $w[] = '(r.ref_no LIKE ? OR r.village LIKE ? OR r.farmer_name LIKE ? OR r.site_name LIKE ? OR r.recorder_name LIKE ?)';
        array_push($a, ...array_fill(0, 5, '%' . $f['q'] . '%'));
    }
    return [$f, implode(' AND ', $w), $a];
}

function dm_fmt_num($v, int $dp = 1): string
{
    if ($v === null || $v === '') return '—';
    return rtrim(rtrim(number_format((float) $v, $dp), '0'), '.');
}
