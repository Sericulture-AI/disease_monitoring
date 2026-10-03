<?php
/**
 * =============================================================================
 * Disease Monitoring — a small PDF writer
 * =============================================================================
 *
 * No library to install: XAMPP has nothing for PDFs out of the box, and a
 * Composer dependency is one more thing to break on the live server. This
 * writes plain PDF 1.4 with the two standard Helvetica fonts (built into every
 * PDF reader, so nothing is embedded), lines, filled boxes, word-wrapped text,
 * tables that run over pages, and JPEG photographs.
 *
 * Text is converted to Windows-1252, which is what the standard fonts can
 * show. Characters outside it (Assamese, Hindi) become '?'. Use English on
 * the form for anything that must appear in the PDF.
 * -----------------------------------------------------------------------------
 */

declare(strict_types=1);

final class DmPdf
{
    public const W = 595.28;   // A4, points
    public const H = 841.89;

    private const WIDTH_R = [278,278,355,556,556,889,667,191,333,333,389,584,278,333,278,278,556,556,556,556,556,556,556,556,556,556,278,278,584,584,584,556,1015,667,667,722,722,667,611,778,722,278,500,667,556,833,722,778,667,778,722,667,611,722,667,944,667,667,611,278,278,278,469,556,333,556,556,500,556,556,278,556,556,222,222,500,222,833,556,556,556,556,333,500,278,556,500,722,500,500,500,334,260,334,584,556,556,556,222,556,333,1000,556,556,333,1000,667,333,1000,556,611,556,556,222,222,333,333,350,556,1000,333,1000,500,333,944,556,500,667,278,333,556,556,556,556,260,556,333,737,370,556,584,333,737,552,400,549,333,333,333,576,537,333,333,333,365,556,834,834,834,611,667,667,667,667,667,667,1000,722,667,667,667,667,278,278,278,278,722,722,778,778,778,778,778,584,778,722,722,722,722,667,667,611,556,556,556,556,556,556,889,500,556,556,556,556,278,278,278,278,556,556,556,556,556,556,556,549,611,556,556,556,556,500,556,500];
    private const WIDTH_B = [278,333,474,556,556,889,722,238,333,333,389,584,278,333,278,278,556,556,556,556,556,556,556,556,556,556,333,333,584,584,584,611,975,722,722,722,722,667,611,778,722,278,556,722,611,833,722,778,667,778,722,667,611,722,667,944,667,667,611,333,278,333,584,556,333,556,611,556,611,556,333,611,611,278,278,556,278,889,611,611,611,611,389,556,333,611,556,778,556,556,500,389,280,389,584,556,556,556,278,556,500,1000,556,556,333,1000,667,333,1000,556,611,556,556,278,278,500,500,350,556,1000,333,1000,556,333,944,556,500,667,278,333,556,556,556,556,280,556,333,737,370,556,584,333,737,552,400,549,333,333,333,576,556,333,333,333,365,556,834,834,834,611,722,722,722,722,722,722,1000,722,667,667,667,667,278,278,278,278,722,722,778,778,778,778,778,584,778,722,722,722,722,667,667,611,556,556,556,556,556,556,889,556,556,556,556,556,278,278,278,278,611,611,611,611,611,611,611,549,611,611,611,611,611,556,611,556];

    private array $pages = [];      // content streams
    private array $images = [];     // [name => [w, h, data]]
    private string $buf = '';
    private bool $bold = false;
    private float $size = 10;
    private array $rgb = [0, 0, 0];

    public float $margin = 42;
    public float $y = 0;            // current baseline, measured from the TOP
    public ?\Closure $onPage = null; // drawn at the top of every new page

    /* ------------------------------------------------------------ pages */

    public function addPage(): void
    {
        if ($this->buf !== '' || $this->pages) $this->pages[] = $this->buf;
        $this->buf = '';
        $this->y = $this->margin;
        if ($this->onPage) ($this->onPage)($this);
    }

    /** Start a new page if fewer than $need points remain. */
    public function need(float $need): void
    {
        if ($this->y + $need > self::H - $this->margin - 20) $this->addPage();
    }

    /* ------------------------------------------------------------ state */

    public function font(bool $bold, float $size): void { $this->bold = $bold; $this->size = $size; }
    public function color(int $r, int $g, int $b): void { $this->rgb = [$r, $g, $b]; }

    /* ------------------------------------------------------------ text */

    public static function enc(string $s): string
    {
        $s = strtr($s, ['—' => '-', '–' => '-', '‘' => "'", '’' => "'", '“' => '"', '”' => '"', '…' => '...', '≥' => '>=', '≤' => '<=', '×' => 'x']);
        $o = @iconv('UTF-8', 'windows-1252//TRANSLIT', $s);
        return $o === false ? preg_replace('/[^\x20-\x7E]/', '?', $s) : $o;
    }

    public function width(string $s, ?bool $bold = null, ?float $size = null): float
    {
        $tbl = ($bold ?? $this->bold) ? self::WIDTH_B : self::WIDTH_R;
        $w = 0;
        $e = self::enc($s);
        for ($i = 0, $n = strlen($e); $i < $n; $i++) {
            $c = ord($e[$i]);
            $w += $c >= 32 ? $tbl[$c - 32] : 556;
        }
        return $w * ($size ?? $this->size) / 1000;
    }

    private static function esc(string $s): string
    {
        return str_replace(['\\', '(', ')', "\r"], ['\\\\', '\\(', '\\)', ''], $s);
    }

    /** One line of text at (x, baseline y from top). */
    public function text(float $x, float $y, string $s): void
    {
        [$r, $g, $b] = $this->rgb;
        $this->buf .= sprintf("BT /%s %.2F Tf %.3F %.3F %.3F rg %.2F %.2F Td (%s) Tj ET\n",
            $this->bold ? 'F2' : 'F1', $this->size, $r / 255, $g / 255, $b / 255,
            $x, self::H - $y, self::esc(self::enc($s)));
    }

    /** Break text into lines no wider than $w. */
    public function wrap(string $s, float $w): array
    {
        $out = [];
        foreach (preg_split('/\r?\n/', $s) ?: [''] as $para) {
            $line = '';
            foreach (preg_split('/ +/', trim($para)) ?: [] as $word) {
                $try = $line === '' ? $word : $line . ' ' . $word;
                if ($this->width($try) <= $w) { $line = $try; continue; }
                if ($line !== '') $out[] = $line;
                /* a single word wider than the column is cut */
                while ($this->width($word) > $w && mb_strlen($word) > 1) {
                    $cut = mb_strlen($word);
                    while ($cut > 1 && $this->width(mb_substr($word, 0, $cut)) > $w) $cut--;
                    $out[] = mb_substr($word, 0, $cut);
                    $word = mb_substr($word, $cut);
                }
                $line = $word;
            }
            $out[] = $line;
        }
        return $out;
    }

    /** A wrapped paragraph from the current y, moving y down; breaks pages. */
    public function para(string $s, float $x = -1, float $w = -1, float $lead = 1.35): void
    {
        $x = $x < 0 ? $this->margin : $x;
        $w = $w < 0 ? self::W - 2 * $this->margin : $w;
        foreach ($this->wrap($s, $w) as $ln) {
            $this->need($this->size * $lead);
            $this->y += $this->size * $lead;
            $this->text($x, $this->y, $ln);
        }
    }

    /* ------------------------------------------------------------ shapes */

    public function rect(float $x, float $y, float $w, float $h, ?array $fill = null, ?array $stroke = null, float $lw = 0.5): void
    {
        $op = '';
        if ($fill)   $op .= sprintf('%.3F %.3F %.3F rg ', $fill[0] / 255, $fill[1] / 255, $fill[2] / 255);
        if ($stroke) $op .= sprintf('%.3F %.3F %.3F RG %.2F w ', $stroke[0] / 255, $stroke[1] / 255, $stroke[2] / 255, $lw);
        $paint = $fill && $stroke ? 'B' : ($fill ? 'f' : 'S');
        $this->buf .= sprintf("q %s%.2F %.2F %.2F %.2F re %s Q\n", $op, $x, self::H - $y - $h, $w, $h, $paint);
    }

    public function line(float $x1, float $y1, float $x2, float $y2, array $rgb = [200, 200, 200], float $lw = 0.5): void
    {
        $this->buf .= sprintf("q %.3F %.3F %.3F RG %.2F w %.2F %.2F m %.2F %.2F l S Q\n",
            $rgb[0] / 255, $rgb[1] / 255, $rgb[2] / 255, $lw, $x1, self::H - $y1, $x2, self::H - $y2);
    }

    /* ------------------------------------------------------------ table */

    /**
     * A table from the current y. $cols = [[title, width], ...]; $rows are
     * arrays of strings (or ['text' => .., 'fill' => [r,g,b], 'bold' => bool]).
     * The header repeats on every page the table runs onto.
     */
    public function table(array $cols, array $rows, float $size = 8.5, array $head = [122, 20, 66]): void
    {
        $pad = 4; $lead = $size * 1.3;
        $x0 = $this->margin;

        $hasHead = (bool) array_filter(array_column($cols, 0), fn($t) => $t !== '');
        $drawHead = function () use ($cols, $size, $pad, $head, $x0, $hasHead) {
            if (!$hasHead) return;
            $h = $size * 1.3 + 2 * $pad;
            $this->need($h + 20);
            $x = $x0;
            foreach ($cols as [$t, $w]) {
                $this->rect($x, $this->y, $w, $h, $head);
                $this->font(true, $size); $this->color(255, 255, 255);
                $this->text($x + $pad, $this->y + $pad + $size, $t);
                $x += $w;
            }
            $this->y += $h;
            $this->color(0, 0, 0);
        };
        $drawHead();

        foreach ($rows as $ri => $row) {
            $cells = []; $maxLines = 1;
            foreach ($cols as $ci => [$t, $w]) {
                $c = $row[$ci] ?? '';
                $c = is_array($c) ? $c : ['text' => (string) $c];
                $this->font(!empty($c['bold']), $size);
                $c['lines'] = $this->wrap((string) $c['text'], $w - 2 * $pad);
                $maxLines = max($maxLines, count($c['lines']));
                $cells[] = $c;
            }
            $h = $maxLines * $lead + 2 * $pad - 2;
            if ($this->y + $h > self::H - $this->margin - 20) { $this->addPage(); $drawHead(); }

            $x = $x0;
            foreach ($cols as $ci => [$t, $w]) {
                $c = $cells[$ci];
                $fill = $c['fill'] ?? ($ri % 2 ? [246, 246, 246] : [255, 255, 255]);
                $this->rect($x, $this->y, $w, $h, $fill, [225, 225, 225], 0.4);
                $this->font(!empty($c['bold']), $size);
                $this->color(...($c['color'] ?? [30, 30, 30]));
                foreach ($c['lines'] as $li => $ln) $this->text($x + $pad, $this->y + $pad + $size + $li * $lead - 1, $ln);
                $x += $w;
            }
            $this->color(0, 0, 0);
            $this->y += $h;
        }
    }

    /* ------------------------------------------------------------ images */

    /** Place a JPEG file, scaled to fit $maxW x $maxH. Returns false if unusable. */
    public function jpeg(string $file, float $x, float $maxW, float $maxH): bool
    {
        $info = @getimagesize($file);
        if (!$info || ($info[2] ?? 0) !== IMAGETYPE_JPEG) return false;
        [$pw, $ph] = $info;
        $cs = ($info['channels'] ?? 3) === 1 ? 'DeviceGray' : (($info['channels'] ?? 3) === 4 ? 'DeviceCMYK' : 'DeviceRGB');
        $name = 'Im' . (count($this->images) + 1);
        $this->images[$name] = [$pw, $ph, (string) file_get_contents($file), $cs];

        $s = min($maxW / $pw, $maxH / $ph, 1.0);
        $w = $pw * $s; $h = $ph * $s;
        $this->need($h + 6);
        $this->buf .= sprintf("q %.2F 0 0 %.2F %.2F %.2F cm /%s Do Q\n", $w, $h, $x, self::H - $this->y - $h, $name);
        $this->y += $h;
        return true;
    }

    /* ------------------------------------------------------------ output */

    /** The finished file as a string. $footer($pageNo, $pageCount) is drawn on each page. */
    public function output(string $title = '', ?\Closure $footer = null): string
    {
        $this->pages[] = $this->buf;
        $this->buf = '';
        $n = count($this->pages);
        if ($footer) {
            foreach ($this->pages as $i => $p) {
                $this->buf = '';
                $footer($this, $i + 1, $n);
                $this->pages[$i] = $p . $this->buf;
            }
            $this->buf = '';
        }

        $obj = [];          // object number => body
        $obj[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $obj[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $obj[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        $next = 5;

        $xobj = '';
        foreach ($this->images as $name => [$w, $h, $data, $cs]) {
            $obj[$next] = "<< /Type /XObject /Subtype /Image /Width $w /Height $h /ColorSpace /$cs"
                . ($cs === 'DeviceCMYK' ? ' /Decode [1 0 1 0 1 0 1 0]' : '')
                . " /BitsPerComponent 8 /Filter /DCTDecode /Length " . strlen($data) . " >>\nstream\n$data\nendstream";
            $xobj .= "/$name $next 0 R ";
            $next++;
        }
        $res = '<< /Font << /F1 3 0 R /F2 4 0 R >>' . ($xobj !== '' ? " /XObject << $xobj>>" : '') . ' >>';

        $kids = [];
        foreach ($this->pages as $content) {
            $z = function_exists('gzcompress') ? gzcompress($content) : null;
            $obj[$next] = $z !== null
                ? '<< /Length ' . strlen($z) . " /Filter /FlateDecode >>\nstream\n" . $z . "\nendstream"
                : '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
            $cid = $next++;
            $obj[$next] = sprintf('<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources %s /Contents %d 0 R >>',
                                  self::W, self::H, $res, $cid);
            $kids[] = $next . ' 0 R';
            $next++;
        }
        $obj[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($kids) . ' >>';
        $obj[$next] = '<< /Title (' . self::esc(self::enc($title)) . ') /Producer (CMER&TI Disease Monitoring) /CreationDate (D:' . date('YmdHis') . ') >>';
        $info = $next;
        ksort($obj);

        $out = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $off = [];
        foreach ($obj as $i => $body) {
            $off[$i] = strlen($out);
            $out .= "$i 0 obj\n$body\nendobj\n";
        }
        $xref = strlen($out);
        $max = max(array_keys($obj));
        $out .= "xref\n0 " . ($max + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i <= $max; $i++) $out .= sprintf("%010d 00000 n \n", $off[$i] ?? 0);
        $out .= "trailer\n<< /Size " . ($max + 1) . " /Root 1 0 R /Info $info 0 R >>\nstartxref\n$xref\n%%EOF\n";
        return $out;
    }
}
