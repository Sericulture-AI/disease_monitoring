<?php
/**
 * Disease Monitoring module — page footer.
 *
 * Closes the content column, shows the other pages of the same stage and
 * the next stage, and loads the one script the copied pages use
 * (js/three-d.js, for the muga crop wheel).
 */
$__same = [];
$__next = null;
if ($STAGE !== null) {
    foreach ($DM_STAGES[$STAGE][2] as [$sec, $slug, $title]) {
        if ($sec . '/' . $slug !== $KEY) $__same[] = [$sec, $slug, $title];
    }
    $__keys = array_keys($DM_STAGES);
    $__i    = array_search($STAGE, $__keys);
    if ($__i !== false && isset($__keys[$__i + 1])) $__next = $__keys[$__i + 1];
}
?>
<?php if ($__same || $__next !== null): ?>
  <aside class="dm-pager" aria-label="Continue through the mechanism">
    <?php if ($__same): ?>
      <div>
        <h2>Also in stage <?php echo $STAGE; ?></h2>
        <ul>
          <?php foreach ($__same as [$sec, $slug, $title]): ?>
            <li><a href="<?php echo esc(page_link($sec, $slug)); ?>"><?php echo $title; ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <?php if ($__next !== null): [$nName, $nWhat, $nPages] = $DM_STAGES[$__next]; ?>
      <div>
        <h2>Next: stage <?php echo $__next; ?></h2>
        <p><a href="<?php echo esc(page_link($nPages[0][0], $nPages[0][1])); ?>"><?php echo $nName; ?></a>
           &mdash; <?php echo $nWhat; ?></p>
      </div>
    <?php endif; ?>
  </aside>
<?php endif; ?>

</div><!-- /.content -->
</main>

<footer class="dm-foot">
  <div class="inner">
    Extracted from the CMER&amp;TI website (cmerti-site). Page text is unchanged from the source;
    see <code>README.md</code> and <code>MECHANISM.md</code> in this folder.
  </div>
</footer>

<script src="<?php echo esc(asset('/js/three-d.js')); ?>" defer></script>
</body>
</html>
