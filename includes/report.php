<?php
/**
 * Disease Monitoring — the PDF report for one record, and sending it.
 */

declare(strict_types=1);

require_once __DIR__ . '/dm-data.php';
require_once __DIR__ . '/pdf.php';
require_once __DIR__ . '/mailer.php';

const DM_WINE = [122, 20, 66];
const DM_SEV_FILL = [
    'high'     => [248, 215, 218],
    'moderate' => [255, 236, 200],
    'low'      => [221, 240, 221],
    'none'     => [235, 235, 235],
];

/** Build the PDF for a record. Returns the bytes. */
function dm_build_pdf(array $r): string
{
    $find = dm_findings((int) $r['id']);
    $all  = dm_diseases();

    $pdf = new DmPdf();
    $W   = DmPdf::W - 2 * $pdf->margin;

    $pdf->onPage = function (DmPdf $p) use ($r, $W) {
        $p->rect(0, 0, DmPdf::W, 54, DM_WINE);
        $p->font(true, 13); $p->color(255, 255, 255);
        $p->text($p->margin, 24, 'Central Muga Eri Research & Training Institute, Lahdoigarh');
        $p->font(false, 9);
        $p->text($p->margin, 40, 'Central Silk Board, Ministry of Textiles, Govt. of India  |  Disease Monitoring Report');
        $p->font(true, 10);
        $ref = (string) $r['ref_no'];
        $p->text(DmPdf::W - $p->margin - $p->width($ref), 24, $ref);
        $p->color(0, 0, 0);
        $p->y = 74;
    };
    $pdf->addPage();

    /* ---- title band with the overall grade */
    $ov = (string) $r['overall'];
    $pdf->rect($pdf->margin, $pdf->y, $W, 34, DM_SEV_FILL[$ov] ?? DM_SEV_FILL['none']);
    $pdf->font(true, 14);
    $pdf->text($pdf->margin + 10, $pdf->y + 22, ucfirst((string) $r['crop']) . ' - ' . $r['district'] . ', ' . $r['state']);
    $lab = 'Overall: ' . strtoupper(dm_severity_label($ov));
    $pdf->font(true, 11);
    $pdf->text($pdf->margin + $W - 10 - $pdf->width($lab), $pdf->y + 22, $lab);
    $pdf->y += 34;

    $section = function (string $t) use ($pdf, $W) {
        $pdf->need(50);
        $pdf->y += 24;
        $pdf->font(true, 11); $pdf->color(...DM_WINE);
        $pdf->text($pdf->margin, $pdf->y, $t);
        $pdf->line($pdf->margin, $pdf->y + 4, $pdf->margin + $W, $pdf->y + 4, [227, 195, 211], 0.8);
        $pdf->color(0, 0, 0);
        $pdf->y += 10;
    };
    $kv = function (array $pairs) use ($pdf, $W) {
        $rows = [];
        $pairs = array_values(array_filter($pairs, fn($p) => $p[1] !== null && $p[1] !== '' && $p[1] !== '—'));
        for ($i = 0; $i < count($pairs); $i += 2) {
            $rows[] = [['text' => $pairs[$i][0], 'bold' => true], (string) $pairs[$i][1],
                       ['text' => $pairs[$i + 1][0] ?? '', 'bold' => true], (string) ($pairs[$i + 1][1] ?? '')];
        }
        $q = $W / 4;
        $pdf->table([['', $q * 0.8], ['', $q * 1.2], ['', $q * 0.8], ['', $q * 1.2]], $rows, 9, [255, 255, 255]);
    };

    $section('Observation');
    $kv([
        ['Reference', $r['ref_no']],
        ['Date observed', date('d M Y', strtotime((string) $r['obs_date']))],
        ['Crop', ucfirst((string) $r['crop'])],
        ['Crop / season', $r['crop_season']],
        ['Site type', $r['site_type']],
        ['Stage observed', $r['stage']],
        ['Host plant', $r['host_plant']],
        ['DFLs', $r['dfls']],
        ['Sample examined', $r['sample_size']],
        ['Recorded by', $r['recorder_name']],
    ]);

    $section('Location');
    $kv([
        ['State', $r['state']],
        ['District', $r['district']],
        ['Block', $r['block']],
        ['Village', $r['village']],
        ['Site / farm', $r['site_name']],
        ['Farmer', $r['farmer_name']],
        ['Farmer phone', $r['farmer_phone']],
        ['GPS', $r['latitude'] !== null ? $r['latitude'] . ', ' . $r['longitude'] : ''],
    ]);

    $section('Findings');
    if (!$find) {
        $pdf->font(false, 10);
        $pdf->para('No disease or pest was recorded on this visit.');
    } else {
        $rows = [];
        foreach ($find as $f) {
            $rows[] = [
                ['text' => dm_disease_label($f['disease_key']), 'bold' => true],
                dm_fmt_num($f['incidence_pct'], 2) . ' %',
                ['text' => dm_severity_label($f['severity']), 'bold' => true, 'fill' => DM_SEV_FILL[$f['severity']]],
                $all[$f['disease_key']][3] ?? '',
            ];
        }
        $pdf->table([['Disease / pest', 120], ['Incidence', 52], ['Severity', 56], ['Recommended management', $W - 228]], $rows, 8.5);
    }

    if ($r['mme_examined']) {
        $section('Mother moth examination (pebrine)');
        $pos = (int) $r['mme_positive']; $ex = (int) $r['mme_examined'];
        $kv([['Moths examined', $ex], ['Positive', $pos . ' (' . dm_fmt_num(100 * $pos / max(1, $ex), 2) . ' %)']]);
    }

    if ($r['temp_min'] !== null || $r['temp_max'] !== null || $r['rh_min'] !== null || $r['rh_max'] !== null || $r['rainfall_mm'] !== null) {
        $section('Weather at the site');
        $kv([
            ['Temperature', dm_fmt_num($r['temp_min']) . ' - ' . dm_fmt_num($r['temp_max']) . ' °C'],
            ['Relative humidity', ($r['rh_min'] ?? '—') . ' - ' . ($r['rh_max'] ?? '—') . ' %'],
            ['Rainfall', $r['rainfall_mm'] !== null ? dm_fmt_num($r['rainfall_mm']) . ' mm' : ''],
        ]);
    }

    if (trim((string) $r['flags']) !== '') {
        $section('Alerts');
        $pdf->font(false, 9.5); $pdf->color(140, 20, 30);
        foreach (explode("\n", (string) $r['flags']) as $fl) $pdf->para('•  ' . $fl);
        $pdf->color(0, 0, 0);
    }

    if (trim((string) $r['action_taken']) !== '') {
        $section('Action taken / advised on the spot');
        $pdf->font(false, 9.5);
        $pdf->para((string) $r['action_taken']);
    }
    if (trim((string) $r['remarks']) !== '') {
        $section('Remarks');
        $pdf->font(false, 9.5);
        $pdf->para((string) $r['remarks']);
    }

    if ($r['photo']) {
        $file = dm_storage('photos') . '/' . basename((string) $r['photo']);
        if (is_file($file) && (getimagesize($file)[2] ?? 0) === IMAGETYPE_JPEG) {
            $section('Photograph');
            $pdf->y += 4;
            $pdf->jpeg($file, $pdf->margin, $W, 300);
        }
    }

    $footer = function (DmPdf $p, int $i, int $n) use ($r) {
        $p->line($p->margin, DmPdf::H - 34, DmPdf::W - $p->margin, DmPdf::H - 34);
        $p->font(false, 7.5); $p->color(110, 110, 110);
        $p->text($p->margin, DmPdf::H - 22, 'Generated ' . date('d M Y, H:i') . ' from record ' . $r['ref_no']
                 . ' (entered ' . date('d M Y H:i', strtotime((string) $r['created_at'])) . ')');
        $t = "Page $i of $n";
        $p->text(DmPdf::W - $p->margin - $p->width($t), DmPdf::H - 22, $t);
    };

    return $pdf->output('Disease Monitoring ' . $r['ref_no'], $footer);
}

/** Build, store and remember the PDF. Returns its absolute path. */
function dm_store_pdf(array $r): string
{
    $name = preg_replace('/[^A-Za-z0-9-]/', '', (string) $r['ref_no']) . '.pdf';
    $path = dm_storage('pdf') . '/' . $name;
    file_put_contents($path, dm_build_pdf($r));
    if (($r['pdf_file'] ?? '') !== $name) {
        dm_run('UPDATE ' . dm_table('records') . ' SET pdf_file = ? WHERE id = ?', [$name, (int) $r['id']]);
    }
    return $path;
}

/** Everyone who should get this record. */
function dm_recipients(array $r): array
{
    $list = [];
    foreach (preg_split('/[\s,;]+/', (string) $r['notify_emails']) ?: [] as $e) if ($e !== '') $list[strtolower($e)] = $e;
    foreach ((array) dm_setting('mail.always_notify', []) as $e) if (filter_var($e, FILTER_VALIDATE_EMAIL)) $list[strtolower($e)] = $e;
    if (dm_setting('mail.copy_to_recorder', true)) {
        $u = dm_one('SELECT email FROM ' . dm_diary_table('users') . ' WHERE id = ?', [(int) $r['user_id']]);
        if ($u && filter_var((string) $u['email'], FILTER_VALIDATE_EMAIL)) $list[strtolower($u['email'])] = $u['email'];
    }
    return array_values($list);
}

/**
 * Generate the PDF and e-mail it to every recipient (or only to $only).
 * Logs one row per address.
 * Returns ['sent' => n, 'failed' => n, 'errors' => [...]].
 */
function dm_notify(array $r, ?int $byUser = null, ?array $only = null): array
{
    $path = dm_store_pdf($r);
    $r    = dm_record((int) $r['id']) ?? $r;
    $to   = $only ?? dm_recipients($r);
    $res  = ['sent' => 0, 'failed' => 0, 'errors' => []];
    if (!$to) return $res;

    $find = dm_findings((int) $r['id']);
    $sev  = strtoupper(dm_severity_label((string) $r['overall']));
    $subj = sprintf('[Disease Monitoring] %s - %s, %s - %s', $r['ref_no'], ucfirst((string) $r['crop']), $r['district'], $sev);

    $lines = [
        'A disease monitoring record has been entered.',
        '',
        'Reference : ' . $r['ref_no'],
        'Date      : ' . date('d M Y', strtotime((string) $r['obs_date'])),
        'Crop      : ' . ucfirst((string) $r['crop']) . ($r['crop_season'] ? ' - ' . $r['crop_season'] : ''),
        'Location  : ' . implode(', ', array_filter([$r['village'], $r['block'], $r['district'], $r['state']])),
        'Stage     : ' . $r['stage'],
        'Overall   : ' . $sev,
        'Recorded  : ' . $r['recorder_name'],
        '',
        'Findings:',
    ];
    if (!$find) $lines[] = '  none recorded';
    foreach ($find as $f) $lines[] = sprintf('  - %s: %s %% (%s)', dm_disease_label($f['disease_key']),
                                              dm_fmt_num($f['incidence_pct'], 2), dm_severity_label($f['severity']));
    if (trim((string) $r['flags']) !== '') {
        $lines[] = ''; $lines[] = 'Alerts:';
        foreach (explode("\n", (string) $r['flags']) as $fl) $lines[] = '  ! ' . $fl;
    }
    $lines[] = '';
    $lines[] = 'The full report, with recommended management, is attached as a PDF.';
    $lines[] = '';
    $lines[] = '-- ';
    $lines[] = 'CMER&TI Disease Monitoring (automatic message; please do not reply)';
    $body = implode("\r\n", $lines);

    $pdfName = basename($path);
    $pdfData = (string) file_get_contents($path);

    foreach ($to as $addr) {
        $err = dm_mail_send($addr, $subj, $body, [[$pdfName, 'application/pdf', $pdfData]]);
        dm_run('INSERT INTO ' . dm_table('notifications') . ' (record_id, email, status, error, sent_by, sent_at) VALUES (?, ?, ?, ?, ?, NOW())',
               [(int) $r['id'], $addr, $err === '' ? 'sent' : 'failed', $err === '' ? null : mb_substr($err, 0, 400), $byUser]);
        if ($err === '') $res['sent']++; else { $res['failed']++; $res['errors'][] = "$addr: $err"; }
    }
    return $res;
}
