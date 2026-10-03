<?php
$SECTION = 'research';
$SLUG    = 'crop-protection';
require __DIR__ . '/../../includes/header.php';
?>

<article class="datapage">

  <p class="datapage__intro">
    Infection travels by four routes: diseased and dead silkworms, their faecal matter,
    contaminated leaf and contaminated appliances. Every measure across these pages closes one
    of those four doors. Muga, reared outdoors, has to do it by preparation and sanitation;
    eri, reared indoors, can do much of it through building integrity.
  </p>

  <ul class="dcards">
    <li class="dcard"><a href="<?php echo esc(page_link('research','disinfection')); ?>">Disinfection</a>
      <p>Six disinfectants with working strengths, cautions, and how to prepare each solution.</p></li>
    <li class="dcard"><a href="<?php echo esc(page_link('research','host-plant-diseases')); ?>">Host Plant Diseases</a>
      <p>Som, soalu and castor &mdash; causal organism, peak season, leaf loss and control.</p></li>
    <li class="dcard"><a href="<?php echo esc(page_link('research','silkworm-diseases')); ?>">Silkworm Diseases</a>
      <p>Pebrine, muscardine, aspergillosis, grasserie and flacherie in muga and eri.</p></li>
    <li class="dcard"><a href="<?php echo esc(page_link('research','pests-predators')); ?>">Insect Pests &amp; Predators</a>
      <p>The uzi fly and the standing community of parasitoids and predators from seven families.</p></li>
  </ul>

</article>

<?php require __DIR__ . '/../../includes/footer.php';
