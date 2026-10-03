<?php
/**
 * =============================================================================
 * CMER&TI — optional pictures and documents for the Service pages
 * =============================================================================
 *
 * The Service pages were rebuilt from the old WordPress site, which kept its
 * photographs and PDFs on cmerti.res.in. Those files have to be copied into
 * this site before they can be shown, and until somebody does that the pages
 * must still read correctly rather than displaying a row of broken images.
 *
 * So every picture and every document on those pages goes through this file,
 * which checks the file is actually there and simply leaves the figure out if
 * it is not. Drop the file in and it appears on the next page load; nothing
 * has to be edited.
 *
 *     pictures    images/services/<name>
 *     documents   documents/services/<name>
 *
 * The site's Content-Security-Policy allows images from this site and nowhere
 * else (img-src 'self' data:, in includes/security.php). That is deliberate
 * and worth keeping: it is why a compromised page cannot quietly load a
 * tracking pixel. It also means hot-linking the pictures from the old site
 * would not have worked — they have to be copied across.
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

/** Is this picture present? Returns its URL, or null. */
function svc_img(string $file): ?string
{
    $rel = '/images/services/' . $file;
    return is_file(dirname(__DIR__) . $rel) ? asset($rel) : null;
}

/** Is this document present? Returns its URL, or null. */
function svc_doc(string $file): ?string
{
    $rel = '/documents/services/' . $file;
    return is_file(dirname(__DIR__) . $rel) ? ROOT_URL . $rel : null;
}

/**
 * One picture with a caption, printed only if the file exists.
 *
 * $wide draws it across the full column — right for the forewarning calendars,
 * which are wide charts that become unreadable at half width.
 */
function svc_figure(string $file, string $caption, bool $wide = false): void
{
    $url = svc_img($file);
    if ($url === null) return;
    printf(
        '<figure class="svcfig%s"><img src="%s" alt="%s" loading="lazy" decoding="async">'
        . '<figcaption>%s</figcaption></figure>' . "\n",
        $wide ? ' svcfig--wide' : '',
        esc($url),
        esc(strip_tags($caption)),
        $caption
    );
}

/**
 * A grid of pictures. Each item is [filename, caption]. Missing files are
 * skipped, and if none of them are present nothing is printed at all — not
 * even an empty grid, which would leave a gap nobody could explain.
 */
function svc_gallery(array $items): void
{
    $found = [];
    foreach ($items as [$file, $caption]) {
        $url = svc_img($file);
        if ($url !== null) $found[] = [$url, $caption];
    }
    if (!$found) return;

    echo '<div class="svcgrid">' . "\n";
    foreach ($found as [$url, $caption]) {
        printf(
            '  <figure class="svcfig"><img src="%s" alt="%s" loading="lazy" decoding="async">'
            . '<figcaption>%s</figcaption></figure>' . "\n",
            esc($url), esc(strip_tags($caption)), $caption
        );
    }
    echo '</div>' . "\n";
}

/**
 * A list of documents. Each item is [filename, label, note].
 *
 * A document that has not been copied across yet is still listed, greyed and
 * marked "not yet published" rather than hidden — a visitor who was told the
 * calendar exists should be able to see that it exists and is coming, and
 * staff should be able to see at a glance what is still missing.
 */
function svc_doclist(array $items): void
{
    echo '<ul class="svcdocs">' . "\n";
    foreach ($items as [$file, $label, $note]) {
        $url = svc_doc($file);
        if ($url !== null) {
            printf(
                '  <li><a href="%s">%s</a> <span class="svcdocs__note">%s</span></li>' . "\n",
                esc($url), $label, $note
            );
        } else {
            printf(
                '  <li class="is-missing"><span>%s</span> '
                . '<span class="svcdocs__note">%s &mdash; not yet published on this site</span></li>' . "\n",
                $label, $note
            );
        }
    }
    echo '</ul>' . "\n";
}
