/* Disease Monitoring — small conveniences on the record form. The form works
   without this file: every row is submitted and the server ignores the rows
   that belong to the other crop. */
(function () {
  'use strict';

  var crop = document.getElementById('crop');
  if (crop) {
    var season = document.getElementById('crop_season');
    var apply = function () {
      var c = crop.value;
      document.querySelectorAll('.dm-findings tr[data-crop]').forEach(function (tr) {
        tr.classList.toggle('is-other', tr.getAttribute('data-crop') !== c);
      });
      if (season) {
        Array.prototype.forEach.call(season.options, function (o) {
          var oc = o.getAttribute('data-crop');
          o.hidden = !!oc && oc !== c;
          if (o.hidden && o.selected) season.value = '';
        });
      }
    };
    crop.addEventListener('change', apply);
    apply();
  }

  /* GPS — only offered where the browser can do it (https or localhost). */
  var gps = document.getElementById('gpsBtn');
  if (gps && navigator.geolocation && window.isSecureContext) {
    gps.hidden = false;
    gps.addEventListener('click', function () {
      gps.disabled = true; gps.textContent = 'Locating…';
      navigator.geolocation.getCurrentPosition(function (p) {
        document.getElementById('latitude').value  = p.coords.latitude.toFixed(6);
        document.getElementById('longitude').value = p.coords.longitude.toFixed(6);
        gps.textContent = 'Location filled'; gps.disabled = false;
      }, function () {
        gps.textContent = 'Location not available'; gps.disabled = false;
      }, { enableHighAccuracy: true, timeout: 15000 });
    });
  }

  /* One submit only — a double click must not make two records and two e-mails. */
  var form = document.getElementById('dmForm');
  if (form) {
    form.addEventListener('submit', function () {
      var b = form.querySelector('button[type=submit]');
      if (b) { setTimeout(function () { b.disabled = true; b.textContent = 'Saving and sending…'; }, 0); }
    });
  }

  /* Confirm before resending e-mail. */
  document.querySelectorAll('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      if (!window.confirm(f.getAttribute('data-confirm'))) e.preventDefault();
    });
  });
})();
