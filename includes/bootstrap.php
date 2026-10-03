<?php
/**
 * Disease Monitoring module — bootstrap.
 *
 * Stand-alone replacement for the parts of cmerti-site/includes/menu.php that
 * the disease pages need: ROOT_URL, esc(), page_link(), asset() and the page
 * list. No database, no login, no translation layer — the pages are copied
 * verbatim from cmerti-site and only need these four helpers to render.
 */

declare(strict_types=1);

/* ROOT_URL — the URL of this folder, worked out from where it sits under the
   web root, so it runs as /disease-monitoring or from any other folder name. */
if (!defined('ROOT_URL')) {
    $root = str_replace('\\', '/', (string) realpath(dirname(__DIR__)));
    $doc  = str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $url  = ($doc !== '' && stripos($root, $doc) === 0) ? substr($root, strlen($doc)) : '/disease-monitoring';
    define('ROOT_URL', rtrim($url, '/'));
}

/* Where pages that are NOT part of this module live (the main site). */
if (!defined('MAIN_SITE_URL')) {
    $parent = dirname(ROOT_URL);
    define('MAIN_SITE_URL', (in_array($parent, ['/', '\\', '.'], true) ? '' : $parent) . '/cmerti-site');
}

/**
 * The mechanism, stage by stage. Each stage lists the pages that carry it.
 * section => slug => title. Order here is the order of the navigation.
 */
$DM_STAGES = [
    '1' => ['Forewarn',   'Know what is coming and when to act',
            [['training', 'forewarning',        'Disease Forewarning &amp; Forecasting'],
             ['research', 'muga-calendar',      'Muga Crop Calendar']]],
    '2' => ['Exclude at source', 'Stop disease entering through seed',
            [['research', 'muga-grainage',      'Muga Seed &amp; Grainage (Mother Moth Examination)'],
             ['research', 'eri-grainage',       'Eri Grainage &amp; Seed'],
             ['research', 'eri-chawki',         'Eri Chawki Rearing (Certification)']]],
    '3' => ['Watch the crop', 'Field and rearing-house surveillance and sanitation',
            [['research', 'muga-rearing',       'Muga Rearing'],
             ['research', 'eri-rearing',        'Eri Silkworm Rearing']]],
    '4' => ['Diagnose', 'Symptom &rarr; disease &rarr; causal organism',
            [['research', 'crop-protection',    'Crop Protection (overview)'],
             ['research', 'silkworm-diseases',  'Silkworm Diseases'],
             ['research', 'host-plant-diseases','Host Plant Diseases'],
             ['research', 'pests-predators',    'Insect Pests &amp; Predators']]],
    '5' => ['Control &amp; disinfect', 'Doses, timing and preparation',
            [['research', 'disinfection',       'Disinfection']]],
];

/* Flat page list: "section/slug" => [title, stage number]. */
$DM_PAGES = [];
foreach ($DM_STAGES as $n => [$name, $what, $pages]) {
    foreach ($pages as [$sec, $slug, $title]) {
        $DM_PAGES[$sec . '/' . $slug] = [$title, $n];
    }
}

/** URL of a page — inside this module if it was extracted, otherwise the main site. */
function page_link(string $section, string $slug): string
{
    $local = dirname(__DIR__) . '/pages/' . $section . '/' . $slug . '.php';
    return (is_file($local) ? ROOT_URL : MAIN_SITE_URL) . '/pages/' . $section . '/' . $slug . '.php';
}

/** Escape anything going into HTML. */
function esc(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** URL for a CSS/JS file with its modified time appended (cache busting). */
function asset(string $path): string
{
    $file  = dirname(__DIR__) . $path;
    $mtime = is_file($file) ? filemtime($file) : false;
    return ROOT_URL . $path . ($mtime ? '?v=' . $mtime : '');
}
