<?php
/**
 * Disease Forewarning & Forecasting.
 *
 * Rebuilt from the old WordPress page. The four calendars are wide charts and
 * are held as pictures; includes/service-assets.php leaves each one out until
 * the file has been copied into images/services/, so the page reads correctly
 * either way.
 */
$SECTION = 'training';
$SLUG    = 'forewarning';

require __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/service-assets.php';
?>

<article class="datapage">

  <p class="datapage__intro">
    A forewarning calendar answers one question: <em>what is coming, and when do I have to act?</em>
    Every disease and pest on this page has a season in which it peaks, and every control measure
    below has to be applied <strong>before</strong> that peak rather than during it. Spraying after
    the leaf spot is visible saves very little; the same spray two weeks earlier controls most of
    it. The calendars set the dates; the measures set the doses.
  </p>

  <div class="dstats">
    <div class="dstat"><strong>4</strong><span>Calendars</span></div>
    <div class="dstat"><strong>3</strong><span>Som diseases</span></div>
    <div class="dstat"><strong>2</strong><span>Host plant pests</span></div>
    <div class="dstat"><strong>2</strong><span>Silkworm diseases</span></div>
  </div>

  <!-- ================================================== som diseases -->
  <section class="dsection">
    <h2 class="dsection__h">Forewarning calendar &mdash; som diseases</h2>
    <p class="dsection__note">
      Leaf spot, anthracnose and grey blight on <em>Persea bombycina</em> (som), month by month.
    </p>

    <?php svc_figure(
        'forewarning-som.jpg',
        'Forewarning calendar of som diseases &mdash; peak incidence periods for leaf spot, anthracnose and grey blight.',
        true
    ); ?>

    <h3 class="dsection__h" style="font-size:16px; border:0; padding:0; margin:18px 0 8px">Control measures</h3>

    <div class="svcmeasures">

      <div class="svcmeasure">
        <h3>Leaf spot disease</h3>
        <dl>
          <dt>Cultural</dt>
          <dd>Regular cultural operations, pruning and pollarding prevent the disease.</dd>
          <dt>Chemical</dt>
          <dd>0.1&nbsp;% Dithane M-45 (1&nbsp;g / 1&nbsp;litre), twice at a 15-day interval
              <strong>before</strong> the peak season of incidence. Controls the disease up to 96&nbsp;%.</dd>
        </dl>
      </div>

      <div class="svcmeasure">
        <h3>Anthracnose of som</h3>
        <dl>
          <dt>Mechanical</dt>
          <dd>Pluck the infected parts and destroy them by burning; this destroys the pathogen
              rather than moving it elsewhere in the plantation.</dd>
          <dt>Chemical</dt>
          <dd>0.1&nbsp;% Bavistin (1&nbsp;g / 1&nbsp;litre), twice at a 15-day interval before the
              peak season of incidence. Controls the disease up to 96&nbsp;%.</dd>
        </dl>
      </div>

      <div class="svcmeasure">
        <h3>Grey blight disease</h3>
        <dl>
          <dt>Cultural</dt>
          <dd>Timely cultural operation, pruning and pollarding can prevent the disease.</dd>
          <dt>Chemical</dt>
          <dd>Foliar spray of Bavistin, twice at a 15-day interval. Checks the disease up to
              85.2&nbsp;%.</dd>
        </dl>
      </div>

    </div>

    <div class="dnote">
      <strong>One figure to confirm before this goes to farmers.</strong> The source page gives the
      grey blight spray as &ldquo;0.1&nbsp;% (1&nbsp;g/10&nbsp;L)&rdquo;. Those two are not the same
      strength &mdash; 1&nbsp;g in 1&nbsp;litre is 0.1&nbsp;%, while 1&nbsp;g in 10&nbsp;litres is
      0.01&nbsp;%, a tenth of it. The strength has therefore been left off above rather than a guess
      printed. Please confirm which is correct with the Pathology section and it will be filled in.
    </div>
  </section>

  <!-- ================================================== stem borer -->
  <section class="dsection">
    <h2 class="dsection__h">Forewarning calendar &mdash; muga host plant pest, stem borer</h2>

    <?php svc_figure(
        'forewarning-stem-borer.jpg',
        'Forewarning calendar for stem borer on muga host plants.',
        true
    ); ?>

    <h3 class="dsection__h" style="font-size:16px; border:0; padding:0; margin:18px 0 8px">Control measures</h3>

    <div class="svcmeasures">

      <div class="svcmeasure">
        <h3>Stem borer</h3>
        <dl>
          <dt>Plant extracts</dt>
          <dd>5&ndash;15&nbsp;% plant extract of neem, dhatura, titabahak or castor controls the
              infestation.</dd>
          <dt>Chemical</dt>
          <dd>Plug the borer holes with a cotton swab soaked in 1.5&nbsp;% Nuvan, then plaster the
              openings with mud. The plaster matters as much as the swab &mdash; it holds the
              vapour in the tunnel.</dd>
        </dl>
      </div>

      <div class="svcmeasure">
        <h3>Leaf gall</h3>
        <dl>
          <dt>Cultural</dt>
          <dd>Collect the infested leaves and twigs and destroy them in a scientific way.</dd>
          <dt>If infestation is heavy</dt>
          <dd>Above 25&nbsp;% infestation, pruning and burning of the leaves and twigs is
              essential &mdash; collection alone will not hold it.</dd>
        </dl>
      </div>

    </div>
  </section>

  <!-- ================================================== silkworm disease -->
  <section class="dsection">
    <h2 class="dsection__h">Forewarning calendar &mdash; silkworm disease</h2>
    <p class="dsection__note">
      Muscardine and flacherie across the muga crop year. Jarua, the December&ndash;January
      pre-seed crop, is the crop most exposed to muscardine.
    </p>

    <?php svc_figure(
        'forewarning-silkworm.jpg',
        'Forewarning calendar for diseases of the muga silkworm.',
        true
    ); ?>

    <h3 class="dsection__h" style="font-size:16px; border:0; padding:0; margin:18px 0 8px">Control measures</h3>

    <div class="svcmeasures">

      <div class="svcmeasure">
        <h3>Muscardine</h3>
        <dl>
          <dt>Site</dt>
          <dd>Select a sunny plot for rearing.</dd>
          <dt>Cultural</dt>
          <dd>Disinfect the rearing field properly. Use of nylon net is essential.</dd>
          <dt>Prophylactic spray</dt>
          <dd>Spray LAHDOI thoroughly on the leaves, tree trunk and soil <strong>seven days
              before</strong> brushing of the Jarua crop. After brushing is too late.</dd>
        </dl>
      </div>

      <div class="svcmeasure">
        <h3>Flacherie</h3>
        <dl>
          <dt>Cultural</dt>
          <dd>Disinfect the rearing field properly.</dd>
          <dt>Leaf management</dt>
          <dd>Follow the proper pruning schedule and provide age-specific leaves. Flacherie in
              muga follows unsuitable leaf as often as it follows infection.</dd>
        </dl>
      </div>

    </div>

    <div class="dnote">
      Crop names, brushing dates and larval periods are set out on
      <a href="<?php echo esc(page_link('research','muga-calendar')); ?>">Muga Crop Calendar</a>,
      and the diseases themselves on
      <a href="<?php echo esc(page_link('research','silkworm-diseases')); ?>">Silkworm Diseases</a>.
      Disinfectants, working strengths and how to prepare each solution are on
      <a href="<?php echo esc(page_link('research','disinfection')); ?>">Disinfection</a>.
    </div>
  </section>

  <!-- ================================================== uzi -->
  <section class="dsection">
    <h2 class="dsection__h">Forewarning calendar &mdash; uzi fly</h2>
    <p class="dsection__note">
      The uzi fly (<em>Exorista</em> spp.) is a parasitoid, not a pathogen: it lays on the larva
      and the maggot emerges from the mature worm or the cocoon. Forewarning matters here because
      the only effective response &mdash; netting and trapping &mdash; has to be in place before
      the fly population builds.
    </p>

    <?php svc_figure(
        'forewarning-uzi.jpg',
        'Forewarning calendar for uzi fly infestation of the silkworm.',
        true
    ); ?>

    <p>
      Host families, seasonal build-up and the standing community of parasitoids and predators are
      described on
      <a href="<?php echo esc(page_link('research','pests-predators')); ?>">Insect Pests &amp; Predators</a>.
    </p>
  </section>

  <!-- ================================================== documents -->
  <section class="dsection">
    <h2 class="dsection__h">Calendars to download</h2>
    <p class="dsection__note">
      District-wise and quarter-wise calendars issued by the institute.
    </p>

    <?php svc_doclist([
        ['forewarning-calendar-2016-revised.pdf',
         'Revised Forewarning Calendar 2016 &mdash; host plants and silkworm',
         'Consolidated calendar, all districts'],
        ['forewarning-calendar-2015-16.pdf',
         'Updated disease calendar for muga silkworm and host plants, 2015-16',
         'Districtwise'],
        ['disease-calendar-2014-q3.pdf',
         'Disease calendar for the quarter ending September 2014',
         'Quarterly bulletin'],
    ]); ?>

    <div class="dnote">
      <strong>These calendars are ten years old.</strong> The most recent one carried over from the
      previous website is dated 2016. If the Pathology section has issued a calendar since, it
      should replace these &mdash; a forewarning calendar that a farmer can tell is a decade out of
      date is worse than no calendar, because it costs the institute the credibility of the next
      one. If 2016 genuinely is the current issue, that is worth stating on the page so nobody
      wonders.
    </div>
  </section>

</article>

<?php require __DIR__ . '/../../includes/footer.php';
