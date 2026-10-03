<?php
/**
 * Disease Monitoring module — page header.
 *
 * Same contract as cmerti-site/includes/header.php: a page sets $SECTION and
 * $SLUG, then requires this file. The title and the stage come from
 * $DM_PAGES in bootstrap.php. Stage navigation runs across the top so a
 * reader can move through the mechanism in order.
 */
require_once __DIR__ . '/bootstrap.php';

$SECTION = $SECTION ?? '';
$SLUG    = $SLUG    ?? '';
$KEY     = $SECTION . '/' . $SLUG;
$TITLE   = $TITLE   ?? ($DM_PAGES[$KEY][0] ?? 'Disease Monitoring Mechanism');
$STAGE   = $DM_PAGES[$KEY][1] ?? null;
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo strip_tags($TITLE); ?> — Disease Monitoring — CMER&amp;TI</title>
<link rel="stylesheet" href="<?php echo esc(asset('/css/base.css')); ?>">
<link rel="stylesheet" href="<?php echo esc(asset('/css/three-d.css')); ?>">
<link rel="stylesheet" href="<?php echo esc(asset('/css/sections/training.css')); ?>">
<link rel="stylesheet" href="<?php echo esc(asset('/css/module.css')); ?>">
</head>
<body>

<header class="dm-top">
  <div class="inner dm-top__row">
    <a class="dm-brand" href="<?php echo esc(ROOT_URL); ?>/reference.php">
      <b>Disease Reference</b>
      <span>CMER&amp;TI, Lahdoigarh &mdash; muga &amp; eri</span>
    </a>
    <a class="dm-back" href="<?php echo esc(ROOT_URL); ?>/">Disease Monitoring (sign in) &rarr;</a>
  </div>
  <nav class="inner dm-stages" aria-label="Stages of the mechanism">
    <ol>
      <?php foreach ($DM_STAGES as $n => [$name, $what, $pages]): ?>
        <li class="<?php echo $STAGE == $n ? 'is-current' : ''; ?>">
          <a href="<?php echo esc(page_link($pages[0][0], $pages[0][1])); ?>">
            <i><?php echo $n; ?></i> <?php echo $name; ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ol>
  </nav>
</header>

<main id="main" tabindex="-1">

<?php if ($SECTION !== ''): ?>
  <div class="pagebar">
    <div class="inner">
      <h1><?php echo $TITLE; ?></h1>
      <nav aria-label="Breadcrumb">
        <ol class="crumbs">
          <li><a href="<?php echo esc(ROOT_URL); ?>/reference.php">Reference</a></li>
          <?php if ($STAGE !== null): ?>
            <li>Stage <?php echo $STAGE; ?> &mdash; <?php echo $DM_STAGES[$STAGE][0]; ?></li>
          <?php endif; ?>
          <li aria-current="page"><?php echo $TITLE; ?></li>
        </ol>
      </nav>
    </div>
  </div>
<?php endif; ?>

<div class="inner content">
