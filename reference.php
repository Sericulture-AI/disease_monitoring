<?php
/**
 * Disease Reference — the institute's disease pages in the order the
 * mechanism runs (public; no sign-in). The recording system is index.php.
 *
 * Puts the extracted pages in the order the mechanism runs, and draws the
 * month-by-month risk matrix from the seasons stated on those pages. Every
 * row below cites the page it came from; nothing here is new data.
 */
$SECTION = '';
$SLUG    = '';
$TITLE   = 'Disease Reference';
require __DIR__ . '/includes/header.php';

/* Month-wise risk, taken only from months stated on the source pages.
   2 = peak / main season, 1 = present or watch. Months not stated are left blank. */
$M = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$risk = [
  // [label, host, source page slug, [month => level], action before peak]
  ['Leaf spot',            'Som',   'host-plant-diseases', [1=>1,2=>1,3=>1,4=>1,5=>1,6=>2,7=>2,8=>2,9=>1,10=>1,11=>1,12=>1],
   '0.1 % Dithane / Indofil M-45, two sprays 15 days apart, both before the Jun&ndash;Aug peak'],
  ['Anthracnose',          'Som',   'host-plant-diseases', [7=>2],
   'Pluck and burn infected parts early; 0.1 % Bavistin twice at 15 days before peak (forewarning page) &mdash; host plant page gives Indofil M-45 as prophylactic'],
  ['Grey blight',          'Som',   'host-plant-diseases', [5=>2,6=>2,7=>2,8=>2],
   '0.1 % Bavistin twice at 15-day interval; pruning and pollarding on time'],
  ['Stem borer',           'Som / soalu', 'forewarning', [],
   'Plant extracts 5&ndash;15 %; plug holes with 1.5 % Nuvan swab and mud plaster (season: see calendar image)'],
  ['Brown blight',         'Soalu', 'host-plant-diseases', [9=>2,10=>2],
   'Pluck and burn; copper oxychloride 3 g/L at 15-day intervals'],
  ['Grey blight',          'Soalu', 'host-plant-diseases', [2=>2,3=>2,10=>2,11=>2,12=>2],
   'Pluck and burn; copper oxychloride 3 g/L as prophylactic'],
  ['Seedling blight',      'Castor','host-plant-diseases', [6=>1,7=>2,8=>2,9=>2],
   'Fytolan / Blitox-50 3 g/L at 15-day intervals (7-day safety period before feeding)'],
  ['Alternaria blight',    'Castor','host-plant-diseases', [4=>2,5=>2,6=>2,7=>2],
   'Fytolan / Blitox-50 3 g/L at 15-day intervals'],
  ['Cercospora leaf spot', 'Castor','host-plant-diseases', [5=>2,6=>2,7=>2,8=>2],
   'Dithane Z-78 or Ziram 2 g/L'],
  ['Powdery mildew',       'Castor','host-plant-diseases', [11=>2,12=>2,1=>2,2=>2,3=>2],
   '0.2 % wettable sulphur at 15 days in dry weather (one-month safety period)'],
  ['Muscardine',           'Muga silkworm', 'silkworm-diseases', [12=>2,1=>2,2=>1,3=>1,4=>1],
   'LAHDOI 1 g/L on leaves, trunk and soil 7 days before Jarua brushing, then every 15 days; sunny plot; net. Feb&ndash;Apr risk follows heavy rainfall'],
  ['Uzi fly',              'Muga silkworm', 'pests-predators', [11=>1,12=>1,1=>1,2=>1,3=>2,4=>2],
   'Nylon net in place before build-up; destroy infested larvae and cocoons; release <em>Nesolynx thymus</em> &ge; 3 months ahead'],
  ['Pebrine',              'Eri silkworm',  'silkworm-diseases', [1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1,12=>1],
   'Mother moth examination every grainage cycle; certified DFLs; egg surface sterilisation'],
  ['Flacherie',            'Eri silkworm',  'silkworm-diseases', [1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1,12=>1],
   'Leaf quality, bed hygiene, no overcrowding; heaviest in summer and rains'],
];
?>

<article class="datapage">

  <p class="datapage__intro">
    This folder holds the institute&rsquo;s disease monitoring mechanism for muga and eri as it
    stands on the website: five stages that run in order every crop &mdash;
    <strong>forewarn</strong> (what is due and when), <strong>exclude at source</strong> (keep
    disease out of the seed), <strong>watch the crop</strong> (field and rearing-house
    surveillance and sanitation), <strong>diagnose</strong> (symptom to cause), and
    <strong>control and disinfect</strong> (the dose, the timing and the preparation). The
    pages behind each stage are copied unchanged from cmerti-site.
  </p>

  <section class="dsection">
    <h2 class="dsection__h">The five stages</h2>
    <ol class="dm-flow">
      <?php foreach ($DM_STAGES as $n => [$name, $what, $pages]): ?>
        <li>
          <h3><i><?php echo $n; ?></i><?php echo $name; ?></h3>
          <p><?php echo $what; ?></p>
          <ul>
            <?php foreach ($pages as [$sec, $slug, $title]): ?>
              <li><a href="<?php echo esc(page_link($sec, $slug)); ?>"><?php echo $title; ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
      <?php endforeach; ?>
    </ol>
    <p class="dm-loop">
      The loop closes after stage 5: whatever was found in the crop (stage 3&ndash;4) is the
      evidence for the next forewarning calendar (stage 1). Muscardine is the worked example
      &mdash; the site notes that heavy rainfall has moved its risk into Chotua and early Jethua,
      so <strong>the calendar has to be read against actual weather, not the month alone</strong>.
    </p>
  </section>

  <section class="dsection">
    <h2 class="dsection__h">Month-by-month risk and the action that must come first</h2>
    <p class="dsection__note">
      Built only from the seasons stated on the pages linked in each row. Blank months are
      months the source does not name, not months of no risk.
    </p>
    <div class="dtable-wrap">
      <table class="dtable dm-risk">
        <thead>
          <tr><th>Disease / pest</th><th>Host</th>
            <?php foreach ($M as $m): ?><th class="m"><?php echo $m; ?></th><?php endforeach; ?>
            <th>Act before the peak</th></tr>
        </thead>
        <tbody>
          <?php foreach ($risk as [$label, $host, $src, $mon, $act]): ?>
            <tr>
              <th><a href="<?php echo esc(page_link($src === 'forewarning' ? 'training' : 'research', $src)); ?>"><?php echo $label; ?></a></th>
              <td><?php echo $host; ?></td>
              <?php for ($i = 1; $i <= 12; $i++): $lv = $mon[$i] ?? 0; ?>
                <td class="m"><?php if ($lv): ?><span class="r<?php echo $lv; ?>" title="<?php echo $M[$i-1] . ($lv === 2 ? ' — peak' : ' — present / watch'); ?>"></span><?php endif; ?></td>
              <?php endfor; ?>
              <td><?php echo $act; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="dm-legend"><span class="l2">Peak / main season</span><span class="l1">Present or watch</span></p>
  </section>

  <section class="dsection">
    <h2 class="dsection__h">Research and monitoring programme behind it</h2>
    <?php
      $data = json_decode((string) @file_get_contents(__DIR__ . '/data/disease-projects.json'), true) ?: ['projects' => []];
    ?>
    <div class="dtable-wrap">
      <table class="dtable">
        <thead><tr><th>Project</th><th>First reported</th><th>Last reported</th><th>Status at last report</th></tr></thead>
        <tbody>
          <?php foreach ($data['projects'] as $p):
              $r = $p['reported']; $first = $r[0]; $last = end($r); ?>
            <tr><td><?php echo esc($p['title']); ?></td>
                <td><?php echo esc($first['year']); ?></td>
                <td><?php echo esc($last['year']); ?></td>
                <td><?php echo esc($last['status']); ?> &mdash; <?php echo esc($last['division']); ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="dsection__note">From the annual-report extract (2014-15 to 2024-25). The current
    sanctioned programme lists <strong>ARP 5023 CN &mdash; Muga and Eri silkworm disease monitoring
    and management in northeastern states of India (Mar 2023 &ndash; Feb 2028)</strong>, and the
    regular programme <strong>Forecasting and forewarning for pest and diseases of muga host plants
    and silkworm</strong>, running since April 2014.</p>
  </section>

</article>

<?php require __DIR__ . '/includes/footer.php';
