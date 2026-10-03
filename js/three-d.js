/* =============================================================================
   CMER&TI — three-dimensional components
   =============================================================================
   Partner to css/three-d.css.

   Two jobs only:
     1  pointer-driven tilt on cards
     2  drag-to-turn on the ring and cycle diagrams

   Everything else in the 3D layer is pure CSS. Nothing here is required for
   the diagrams to be readable — with this file absent they still render and
   still rotate on their own.
   ========================================================================== */

(function () {
  'use strict';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var coarse  = window.matchMedia('(pointer: coarse)').matches;


  /* -------------------------------------------------------------- 1 tilt */

  (function () {
    /* Skip on touch: there is no hover to tilt towards, and a tilt triggered
       by a tap just makes the card feel unstable under the finger. */
    if (reduced || coarse) return;

    var SEL = '.card, .dcard, .stats-card, .dstat, .ar-card, .social-card, .tech-card';
    var MAX = 6;                       // degrees; see the note in three-d.css

    document.querySelectorAll(SEL).forEach(function (c) {
      c.classList.add('tilt3d');
    });

    var active = null;

    document.addEventListener('pointermove', function (ev) {
      var card = ev.target.closest && ev.target.closest('.tilt3d');

      if (card !== active && active) {
        active.classList.remove('is-tilting');
        active.style.removeProperty('--rx');
        active.style.removeProperty('--ry');
      }
      active = card;
      if (!card) return;

      var r = card.getBoundingClientRect();
      /* -0.5 … 0.5 from the card's centre */
      var px = (ev.clientX - r.left) / r.width  - 0.5;
      var py = (ev.clientY - r.top)  / r.height - 0.5;

      card.classList.add('is-tilting');
      /* Negated on X so the card leans towards the pointer, not away — the
         opposite reads as the card recoiling. */
      card.style.setProperty('--rx', (-py * MAX * 2).toFixed(2) + 'deg');
      card.style.setProperty('--ry', ( px * MAX * 2).toFixed(2) + 'deg');
    }, { passive: true });

    /* Leaving the window mid-tilt would otherwise strand a card at an angle. */
    document.addEventListener('pointerleave', function () {
      if (!active) return;
      active.classList.remove('is-tilting');
      active.style.removeProperty('--rx');
      active.style.removeProperty('--ry');
      active = null;
    });
  })();


  /* ------------------------------------------------------- 2 drag to turn

     The wheels turn on their own. Dragging lets a reader stop and inspect a
     particular crop or stage, which is the whole reason for laying the data
     out on a wheel rather than in a list. */

  (function () {
    if (reduced) return;

    /* Each component turns about a different axis, and at a different resting
       tilt. Rather than special-casing them one by one, each declares how to
       build its transform from a single accumulated angle. */
    var KINDS = [
      { stage: '.ring3d-stage',   inner: '.ring3d',
        at: function (a) { return 'rotateX(-16deg) rotateY(' + a + 'deg)'; }, from: 0 },

      { stage: '.cycle3d-stage',  inner: '.cycle3d',
        at: function (a) { return 'rotateX(66deg) rotateZ(' + a + 'deg)'; }, from: 0 },

      /* The strata are a genuine 3D stack and turn about Z. The columns and
         staircase are deliberately absent: they are drawn as a fixed
         isometric projection out of skewed flat faces, so applying a rotation
         to them would fight the projection rather than turn the object. They
         move by rising and bobbing instead — see css/three-d.css. */
      { stage: '.strata3d-stage', inner: '.strata3d',
        at: function (a) { return 'rotateX(24deg) rotateZ(' + a + 'deg)'; },
        from: -2, min: -22, max: 18 }
    ];

    KINDS.forEach(function (kind) {
      document.querySelectorAll(kind.stage).forEach(function (stage) {
        bindDrag(stage, stage.querySelector(kind.inner), kind);
      });
    });

    function bindDrag(stage, inner, kind) {
      if (!inner) return;

      var dragging = false;
      var startX = 0;
      var spin = kind.from;

      stage.style.cursor = 'grab';
      stage.style.touchAction = 'pan-y';   // let the page still scroll vertically

      function apply() {
        if (kind.min !== undefined) {
          spin = Math.max(kind.min, Math.min(kind.max, spin));
        }
        inner.style.animation = 'none';
        inner.style.transform = kind.at(spin);
      }

      stage.addEventListener('pointerdown', function (ev) {
        dragging = true;
        startX = ev.clientX;
        stage.style.cursor = 'grabbing';
        stage.setPointerCapture(ev.pointerId);
        apply();
      });

      stage.addEventListener('pointermove', function (ev) {
        if (!dragging) return;
        spin += (ev.clientX - startX) * 0.45;
        startX = ev.clientX;
        apply();
      });

      function release(ev) {
        if (!dragging) return;
        dragging = false;
        stage.style.cursor = 'grab';
        try { stage.releasePointerCapture(ev.pointerId); } catch (e) {}
      }
      stage.addEventListener('pointerup', release);
      stage.addEventListener('pointercancel', release);

      /* Keyboard: the diagram is focusable and the arrow keys step it, so it
         is not mouse-only. */
      stage.setAttribute('tabindex', '0');
      stage.setAttribute('role', 'group');
      stage.addEventListener('keydown', function (ev) {
        if (ev.key !== 'ArrowLeft' && ev.key !== 'ArrowRight') return;
        ev.preventDefault();
        spin += (ev.key === 'ArrowRight' ? 20 : -20);
        apply();
      });

      /* Double-click, or Escape while focused, hands the diagram back to its
         own animation — otherwise a reader who has dragged it into a corner
         has no way of recovering the original view short of reloading. */
      function restore() {
        spin = kind.from;
        inner.style.animation = '';
        inner.style.transform = '';
      }
      stage.addEventListener('dblclick', restore);
      stage.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape') { ev.preventDefault(); restore(); }
      });
    }
  })();

})();
