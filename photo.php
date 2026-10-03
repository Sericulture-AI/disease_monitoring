<?php
/** The photo attached to a record — only for its recorder or an administrator. */
require_once __DIR__ . '/includes/dm-data.php';
$ME  = dm_require_login();
$rec = dm_record_or_404((int) ($_GET['id'] ?? 0), $ME);

$file = dm_storage('photos') . '/' . basename((string) $rec['photo']);
if (!$rec['photo'] || !is_file($file)) { http_response_code(404); exit('No photo.'); }
$type = @getimagesize($file)[2] ?? 0;
header('Content-Type: ' . ($type === IMAGETYPE_PNG ? 'image/png' : 'image/jpeg'));
header('Content-Length: ' . filesize($file));
header('X-Content-Type-Options: nosniff');
readfile($file);
