<?php
/**
 * Disease Monitoring — header for the signed-in pages (and the sign-in page).
 * Expects $TITLE; $ME is the signed-in user or null.
 */
$TITLE = $TITLE ?? 'Disease Monitoring';
$ME    = $ME ?? (function_exists('dm_user') ? dm_user() : null);
$here  = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$nav   = [
    'index.php'      => 'Dashboard',
    'record-new.php' => 'New Record',
    'records.php'    => 'Records',
    'reference.php'  => 'Disease Reference',
];
if ($ME && function_exists('dm_is_admin') && dm_is_admin($ME)) $nav['mail-test.php'] = 'E-mail Check';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?php echo esc(html_entity_decode(strip_tags($TITLE), ENT_QUOTES, "UTF-8")); ?> — Disease Monitoring — CMER&amp;TI</title>
<link rel="stylesheet" href="<?php echo esc(asset('/css/base.css')); ?>">
<link rel="stylesheet" href="<?php echo esc(asset('/css/diary.css')); ?>">
<link rel="stylesheet" href="<?php echo esc(asset('/css/module.css')); ?>">
<link rel="stylesheet" href="<?php echo esc(asset('/css/app.css')); ?>">
</head>
<body>

<header class="dm-top">
  <div class="inner dm-top__row">
    <a class="dm-brand" href="<?php echo esc(dm_url()); ?>">
      <b>Disease Monitoring</b>
      <span>CMER&amp;TI, Lahdoigarh &mdash; muga &amp; eri</span>
    </a>
    <?php if ($ME): ?>
      <form method="post" action="<?php echo esc(dm_url('login.php')); ?>" class="dm-user">
        <span><?php echo esc(dm_name($ME)); ?><?php echo dm_is_admin($ME) ? ' &middot; admin' : ''; ?></span>
        <input type="hidden" name="do" value="logout">
        <?php echo dm_csrf_field(); ?>
        <button type="submit">Sign out</button>
      </form>
    <?php else: ?>
      <a class="dm-back" href="<?php echo esc((string) dm_setting('main_site_url', '/cmerti-site')); ?>/">Main website &rarr;</a>
    <?php endif; ?>
  </div>
  <?php if ($ME): ?>
    <nav class="dm-stages" aria-label="Disease monitoring">
      <ol>
        <?php foreach ($nav as $file => $label): ?>
          <li class="<?php echo $here === $file ? 'is-current' : ''; ?>">
            <a href="<?php echo esc(dm_url($file)); ?>"><?php echo $label; ?></a>
          </li>
        <?php endforeach; ?>
      </ol>
    </nav>
  <?php endif; ?>
</header>

<main id="main" tabindex="-1">
  <div class="pagebar">
    <div class="inner"><h1><?php echo $TITLE; ?></h1></div>
  </div>
  <div class="inner content">
