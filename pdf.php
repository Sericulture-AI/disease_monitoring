<?php
/**
 * The PDF report of one record, for the person who recorded it or an
 * administrator. Rebuilt on every request so it always matches the record.
 * ?dl=1 downloads instead of opening in the browser.
 */
require_once __DIR__ . '/includes/report.php';
$ME  = dm_require_login();
$rec = dm_record_or_404((int) ($_GET['id'] ?? 0), $ME);

$path = dm_store_pdf($rec);
dm_audit(isset($_GET['dl']) ? 'download' : 'view_file', 'disease_pdf', (int) $rec['id'], (string) $rec['ref_no']);

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($path));
header('Content-Disposition: ' . (isset($_GET['dl']) ? 'attachment' : 'inline') . '; filename="' . basename($path) . '"');
header('X-Content-Type-Options: nosniff');
readfile($path);
