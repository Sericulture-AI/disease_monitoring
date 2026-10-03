<?php
/**
 * Sign In — same username and password as the work diary on cmerti-site.
 * Also signs out (the header's button posts here with do=logout).
 */
require_once __DIR__ . '/includes/app.php';

dm_private_page();
$ready = dm_db_ready();
$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && $ready) {
    dm_check_csrf();

    if (($_POST['do'] ?? '') === 'logout') {
        dm_audit('logout', 'user', isset($_SESSION['dm_uid']) ? (int) $_SESSION['dm_uid'] : null, 'disease-monitoring');
        dm_logout();
        header('Location: ' . dm_url('login.php', ['bye' => 1]));
        exit;
    }

    $error = dm_login((string) ($_POST['username'] ?? ''), (string) ($_POST['password'] ?? ''));
    if ($error === '') {
        $back = (string) ($_SESSION['dm_after'] ?? '');
        unset($_SESSION['dm_after']);
        $safe = ($back !== '' && $back[0] === '/' && strpos($back, '//') !== 0 && strpos($back, ROOT_URL . '/') === 0)
              ? $back : dm_url();
        header('Location: ' . $safe);
        exit;
    }
}

if ($ready) { dm_install(); if (dm_user()) { header('Location: ' . dm_url()); exit; } dm_csrf(); }

$TITLE = 'Sign In';
$ME = null;
require __DIR__ . '/includes/app-header.php';
?>

<article class="diary diary--narrow">

  <?php if (!$ready): ?>
    <div class="dnote" role="alert">
      <strong>No database connection.</strong> This module uses the work diary&rsquo;s database.
      Start MySQL in XAMPP, and check that <code>cmerti-site/setup.php</code> has been run once.
    </div>
  <?php else: ?>

    <?php if (isset($_GET['bye'])): ?>
      <p class="diary-flash diary-flash--ok" role="status">You have been signed out.</p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
      <p class="diary-flash diary-flash--bad" role="alert"><?php echo esc($error); ?></p>
    <?php endif; ?>

    <div class="diary-login">
      <form method="post" class="diary-form" autocomplete="on">
        <?php echo dm_csrf_field(); ?>
        <p class="diary-login__lead">
          Sign in with your <strong>work diary</strong> username and password.
          Accounts are created by the administrator &mdash; there is no public registration.
        </p>
        <div class="diary-field">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required autofocus
                 autocapitalize="none" autocorrect="off" spellcheck="false"
                 autocomplete="username" maxlength="60"
                 value="<?php echo esc((string) ($_POST['username'] ?? '')); ?>">
        </div>
        <div class="diary-field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required
                 autocomplete="current-password" maxlength="200">
        </div>
        <button class="btn btn--solid diary-submit" type="submit">Sign In</button>
        <p class="diary-login__note">
          Forgotten your password? An administrator can set a new one from
          <em>User Accounts</em> in the work diary.
        </p>
      </form>

      <aside class="diary-login__aside">
        <h2>What this is for</h2>
        <p>Recording what is found on each field visit, grainage check or chawki inspection
           &mdash; disease, incidence, stage, location and weather &mdash; as a structured record,
           not a note.</p>
        <p>When a record is saved it is graded, turned into a PDF report with the recommended
           management, and e-mailed to the addresses you enter.</p>
        <p class="diary-login__privacy">Your records are visible to you and to administrators.</p>
      </aside>
    </div>

  <?php endif; ?>
</article>

<?php require __DIR__ . '/includes/app-footer.php';
