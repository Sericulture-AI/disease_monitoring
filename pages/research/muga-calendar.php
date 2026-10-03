<?php
$SECTION = 'research';
$SLUG    = 'muga-calendar';
require __DIR__ . '/../../includes/header.php';
?>

<article class="datapage">

  <p class="datapage__intro">
    Muga is planned in six named crops through the year. Two are commercial, two are seed crops
    and two are pre-seed. The pruning columns matter as much as the brushing date &mdash; leaf
    of the right age has to be ready when the worms are, which means the pruning decision is
    taken two to four months before the crop.
  </p>

  <section class="dsection">
    <h2 class="dsection__h">The year as a wheel</h2>

    <?php
    /* Six crops around 360 degrees — 60 apart. Held as an array so the
       geometry follows the data rather than being typed into the markup. */
    $crops = [
        ['Jarua',  'Dec &ndash; Jan', 'Pre-seed',   'preseed'],
        ['Chotua', 'Feb &ndash; Mar', 'Seed',       'seed'],
        ['Jethua', 'Apr &ndash; May', 'Commercial', 'commercial'],
        ['Aherua', 'Jun &ndash; Jul', 'Pre-seed',   'preseed'],
        ['Bhodia', 'Aug &ndash; Sep', 'Seed',       'seed'],
        ['Kotia',  'Oct &ndash; Nov', 'Commercial', 'commercial'],
    ];
    ?>

    <div class="ring3d-stage" aria-label="The six muga crops arranged around the year">
      <div class="ring3d">
        <div class="ring3d__hub"><b>6</b><span>crops a year</span></div>
        <?php foreach ($crops as $i => $c): ?>
          <div class="ring3d__face ring3d__face--<?php echo esc($c[3]); ?>"
               style="--a:<?php echo $i * 60; ?>deg">
            <b><?php echo $c[0]; ?></b>
            <span><?php echo $c[1]; ?></span>
            <i><?php echo $c[2]; ?></i>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <p class="d3-caption">
      Drag to turn the wheel, or use the arrow keys; hovering pauses it. Reading the year round
      rather than down a column makes the alternation visible: a pre-seed crop feeds a seed crop,
      which feeds a commercial crop, twice over. A failure in Jarua &mdash; the crop most exposed
      to muscardine &mdash; is therefore felt two crops later in Jethua.
    </p>
  </section>

  <section class="dsection">
    <h2 class="dsection__h">The six crops</h2>
    <div class="dtable-wrap">
      <table class="dtable">
        <thead><tr><th>Crop</th><th>Season</th><th>Category</th><th>Brushing</th><th>Larval period</th><th>Prune: early age</th><th>Prune: late age</th></tr></thead>
        <tbody>
          <tr><th>Jarua</th><td>Dec &ndash; Jan</td><td>Pre-seed</td><td>3rd wk Dec</td><td>50 &ndash; 55 d</td><td>1st wk Sept</td><td>1st wk Aug</td></tr>
          <tr><th>Chotua</th><td>Feb &ndash; Mar</td><td>Seed</td><td>2nd wk Feb</td><td>40 &ndash; 45 d</td><td>1st&ndash;2nd wk Oct</td><td>1st&ndash;2nd wk Sept</td></tr>
          <tr><th>Jethua</th><td>Apr &ndash; May</td><td>Commercial</td><td>3rd wk Apr</td><td>24 &ndash; 30 d</td><td>1st wk Dec</td><td>1st wk Nov</td></tr>
          <tr><th>Aherua</th><td>Jun &ndash; Jul</td><td>Pre-seed</td><td>4th wk Jun</td><td>22 &ndash; 28 d</td><td>1st wk Mar</td><td>1st wk Feb</td></tr>
          <tr><th>Bhodia</th><td>Aug &ndash; Sep</td><td>Seed</td><td>2nd wk Aug</td><td>25 &ndash; 31 d</td><td>1st wk Apr</td><td>1st wk Mar</td></tr>
          <tr><th>Kotia</th><td>Oct &ndash; Nov</td><td>Commercial</td><td>1st wk Oct</td><td>26 &ndash; 32 d</td><td>1st&ndash;2nd wk Jul</td><td>1st&ndash;2nd wk Jun</td></tr>
        </tbody>
      </table>
    </div>

    <div class="dnote">
      Larval period more than doubles between Jethua (24&ndash;30 days) and Jarua (50&ndash;55
      days). The winter crops are slow, and Jarua is also the crop most exposed to muscardine,
      which can take 60&ndash;90 % of it. The two commercial crops sit in the two most
      favourable windows &mdash; that is not a coincidence but the whole logic of the calendar.
    </div>
  </section>

  <section class="dsection">
    <h2 class="dsection__h">Cocoon quality by crop</h2>
    <div class="dtable-wrap">
      <table class="dtable">
        <thead><tr><th>Crop season</th><th class="num">Cocoon wt. (g)</th><th class="num">Shell wt. (g)</th><th class="num">SR (%)</th><th class="num">Filament length (m)</th><th class="num">Denier</th></tr></thead>
        <tbody>
          <tr><th>Feb &ndash; Mar (Chotua)</th><td class="num">5.00</td><td class="num">0.40</td><td class="num">8.00</td><td class="num">275</td><td class="num">5.00</td></tr>
          <tr><th>Apr &ndash; May (Jethua)</th><td class="num">7.00</td><td class="num">0.60</td><td class="num">8.77</td><td class="num">425</td><td class="num">5.80</td></tr>
          <tr><th>Jun &ndash; Jul (Aherua)</th><td class="num">6.00</td><td class="num">0.50</td><td class="num">8.33</td><td class="num">325</td><td class="num">5.40</td></tr>
          <tr><th>Aug &ndash; Sep (Bhodia)</th><td class="num">5.50</td><td class="num">0.43</td><td class="num">8.18</td><td class="num">300</td><td class="num">5.20</td></tr>
          <tr><th>Oct &ndash; Nov (Kotia)</th><td class="num">8.00</td><td class="num">0.70</td><td class="num">8.75</td><td class="num">450</td><td class="num">6.00</td></tr>
          <tr><th>Dec &ndash; Jan (Jarua)</th><td class="num">6.50</td><td class="num">0.55</td><td class="num">8.46</td><td class="num">350</td><td class="num">5.60</td></tr>
        </tbody>
      </table>
    </div>
    <p class="dsection__note">Kotia gives the heaviest cocoon and longest filament of the year;
    Chotua, the spring seed crop, the lightest and shortest.</p>
  </section>

</article>

<?php require __DIR__ . '/../../includes/footer.php';
