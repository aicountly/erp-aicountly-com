<?php
namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Excel / CSV writer for the Balance Sheet, Profit & Loss and Trial Balance.
 *
 * It only ever consumes the rows the model returns for the screen (the same array the grid renders):
 *   horizontal  l_group_name / l_type / l_amt / l_det   and   r_...   (null = blank cell)
 *   vertical    group_name / type / amt
 *   trial bal.  group_name / parent / debit_total / credit_total  (+ debit / credit text: '' = blank)
 * Amounts are written as real numbers (blank stays blank, nothing is turned into 0), names as explicit strings
 * (a name that starts with "=" can never become a formula). No amount is computed here.
 *
 * The workbook is built completely in memory and only then sent, so a failure can never produce a
 * corrupt attachment.
 */
final class ReportSheetWriter
{
    private const XLSX_FORMAT = '#,##0.00;-#,##0.00';
    private const CSV_FORMAT  = '0.00;-0.00';

    // ------------------------------------------------------------------------------------
    // Public builders
    // ------------------------------------------------------------------------------------

    /**
     * @param array $opt title, subtitle, left_head, right_head, detail (bool: DETAIL columns), sheet, csv (bool), notes (string[])
     */
    public static function horizontal(array $rows, array $opt): Spreadsheet
    {
        [$ss, $sh, $fmt] = self::start($opt);
        $detail = !empty($opt['detail']);
        $cols   = $detail ? ['A', 'B', 'C', 'D', 'E', 'F'] : ['A', 'B', 'C', 'D'];       // name, [detail], amt  x2
        $last   = end($cols);
        [$ln, $ld, $la, $rn, $rd, $ra] = $detail ? $cols : [$cols[0], null, $cols[1], $cols[2], null, $cols[3]];

        self::titleBlock($sh, $opt, "A1:{$last}1", "A2:{$last}2");
        $r = 3;
        $sh->setCellValueExplicit("{$ln}{$r}", (string)($opt['left_head'] ?? 'LEFT'), DataType::TYPE_STRING);
        $sh->setCellValueExplicit("{$rn}{$r}", (string)($opt['right_head'] ?? 'RIGHT'), DataType::TYPE_STRING);
        if ($detail) {
            $sh->setCellValueExplicit("{$ld}{$r}", 'DETAIL', DataType::TYPE_STRING);
            $sh->setCellValueExplicit("{$rd}{$r}", 'DETAIL', DataType::TYPE_STRING);
        }
        $sh->setCellValueExplicit("{$la}{$r}", 'AMT', DataType::TYPE_STRING);
        $sh->setCellValueExplicit("{$ra}{$r}", 'AMT', DataType::TYPE_STRING);
        self::headerStyle($sh, "A{$r}:{$last}{$r}");
        foreach (array_merge([$la, $ra], $detail ? [$ld, $rd] : []) as $c) {
            $sh->getStyle("{$c}{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $sh->freezePane('A' . ($r + 1));
        $r++;

        foreach ($rows as $row) {
            $isTotal = ($row['l_group_name'] ?? '') === '' && ($row['r_group_name'] ?? '') === '' && isset($row['pq_rowattr']);
            foreach ([[$ln, $ld, $la, 'l'], [$rn, $rd, $ra, 'r']] as [$nc, $dc, $ac, $p]) {
                $name = self::cleanLabel((string)($row[$p . '_group_name'] ?? ''));
                $type = (string)($row[$p . '_type'] ?? '');
                if ($name !== '') {
                    $sh->setCellValueExplicit("{$nc}{$r}", $name, DataType::TYPE_STRING);
                    $lvl = self::indent($type);
                    if ($lvl > 0) {
                        $sh->getStyle("{$nc}{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent($lvl);
                    }
                }
                self::putNumber($sh, "{$ac}{$r}", $row[$p . '_amt'] ?? null, $fmt);
                if ($detail) { self::putNumber($sh, "{$dc}{$r}", $row[$p . '_det'] ?? null, $fmt); }

                $style = (string)($row['pq_cellattr'][$p . '_group_name']['style'] ?? '');
                if ($isTotal || $type === 'prt' || $type === 'pl' || $type === 'clo' || $type === 'opn' || stripos($style, 'font-weight:bold') !== false) {
                    $sh->getStyle("{$nc}{$r}:{$ac}{$r}")->getFont()->setBold(true);
                }
            }
            if ($isTotal) { self::totalStyle($sh, "A{$r}:{$last}{$r}"); }
            $r++;
        }
        self::notes($sh, $opt['notes'] ?? [], $r + 1, $last);
        self::widths($sh, $cols, [$ln, $rn]);
        return $ss;
    }

    public static function vertical(array $rows, array $opt): Spreadsheet
    {
        [$ss, $sh, $fmt] = self::start($opt);
        self::titleBlock($sh, $opt, 'A1:B1', 'A2:B2');
        $r = 3;
        $sh->setCellValueExplicit("A{$r}", 'PARTICULARS', DataType::TYPE_STRING);
        $sh->setCellValueExplicit("B{$r}", 'AMT', DataType::TYPE_STRING);
        self::headerStyle($sh, "A{$r}:B{$r}");
        $sh->getStyle("B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sh->freezePane('A' . ($r + 1));
        $r++;

        foreach ($rows as $row) {
            $type = (string)($row['type'] ?? '');
            $name = self::cleanLabel((string)($row['group_name'] ?? ''));
            if ($name !== '') {
                $sh->setCellValueExplicit("A{$r}", $name, DataType::TYPE_STRING);
                $lvl = self::indent($type);
                if ($lvl > 0) {
                    $sh->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent($lvl);
                    $sh->getRowDimension($r)->setOutlineLevel($lvl);
                }
            }
            self::putNumber($sh, "B{$r}", $row['amt'] ?? null, $fmt);
            $style = (string)($row['pq_rowattr']['style'] ?? '') . (string)($row['pq_cellattr']['group_name']['style'] ?? '');
            if ($type === 'hdr' || $type === 'ttl' || in_array($type, ['cat', 'pl', 'prt', 'np_cd', 'nl_cd', 'gp_cf', 'gr_cf', 'prt_hd', 'opn', 'clo'], true)
                || stripos($style, 'font-weight:bold') !== false) {
                $sh->getStyle("A{$r}:B{$r}")->getFont()->setBold(true);
            }
            if ($type === 'hdr') { self::fill($sh, "A{$r}:B{$r}", 'E6E6FA'); }
            if ($type === 'ttl') { self::totalStyle($sh, "A{$r}:B{$r}"); }
            $r++;
        }
        $sh->setShowSummaryBelow(false);
        self::notes($sh, $opt['notes'] ?? [], $r + 1, 'B');
        self::widths($sh, ['A', 'B'], ['A']);
        return $ss;
    }

    public static function trialBalance(array $rows, array $opt): Spreadsheet
    {
        [$ss, $sh, $fmt] = self::start($opt);
        self::titleBlock($sh, $opt, 'A1:D1', 'A2:D2');
        $r = 3;
        foreach (['A' => 'GROUP / ACCOUNT / BSD', 'B' => 'PARENT', 'C' => 'DEBIT', 'D' => 'CREDIT'] as $c => $h) {
            $sh->setCellValueExplicit("{$c}{$r}", $h, DataType::TYPE_STRING);
        }
        self::headerStyle($sh, "A{$r}:D{$r}");
        $sh->getStyle("C{$r}:D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sh->freezePane('A' . ($r + 1));
        $r++;

        $dr = 0.0; $cr = 0.0;
        foreach ($rows as $row) {
            $type = (string)($row['type'] ?? '');
            $sh->setCellValueExplicit("A{$r}", self::cleanLabel((string)($row['group_name'] ?? '')), DataType::TYPE_STRING);
            $sh->setCellValueExplicit("B{$r}", self::cleanLabel((string)($row['parent'] ?? '')), DataType::TYPE_STRING);
            // text '' means the grid shows a blank cell; the number is the very value behind the text
            $d = ((string)($row['debit'] ?? '') === '')  ? null : (float)$row['debit_total'];
            $c = ((string)($row['credit'] ?? '') === '') ? null : (float)$row['credit_total'];
            self::putNumber($sh, "C{$r}", $d, $fmt);
            self::putNumber($sh, "D{$r}", $c, $fmt);
            $dr += (float)($row['debit_total'] ?? 0);
            $cr += (float)($row['credit_total'] ?? 0);
            if ($type === 'cls') { self::fill($sh, "A{$r}:D{$r}", 'E8F5E9'); $sh->getStyle("A{$r}:D{$r}")->getFont()->setBold(true); }
            if ($type === 'opn' || ($type === 'dfs' && (int)($row['group_id'] ?? 0) === 0)) { $sh->getStyle("A{$r}:D{$r}")->getFont()->setBold(true); }
            $r++;
        }
        $dr = round($dr, 2); $cr = round($cr, 2);
        $sh->setCellValueExplicit("A{$r}", 'TOTAL', DataType::TYPE_STRING);
        self::putNumber($sh, "C{$r}", $dr, $fmt, true);
        self::putNumber($sh, "D{$r}", $cr, $fmt, true);
        self::totalStyle($sh, "A{$r}:D{$r}");
        if (abs($dr - $cr) > 0.005) {
            $r++;
            $sh->setCellValueExplicit("A{$r}", 'DIFFERENCE (Debit - Credit)', DataType::TYPE_STRING);
            self::putNumber($sh, "C{$r}", round($dr - $cr, 2), $fmt, true);
            $sh->getStyle("A{$r}:D{$r}")->getFont()->setBold(true)->getColor()->setRGB('C00000');
        }
        self::notes($sh, $opt['notes'] ?? [], $r + 2, 'D');
        self::widths($sh, ['A', 'B', 'C', 'D'], ['A', 'B']);
        return $ss;
    }

    // ------------------------------------------------------------------------------------
    // Output
    // ------------------------------------------------------------------------------------

    /** The file's bytes (used by deliver() and by the tests). */
    public static function render(Spreadsheet $ss, string $format): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'rpt');
        try {
            IOFactory::createWriter($ss, $format === 'csv' ? 'Csv' : 'Xlsx')->save($tmp);
            $bytes = (string)file_get_contents($tmp);
        } finally {
            @unlink($tmp);
        }
        return $bytes;
    }

    /** Build first, send second: nothing is output until the whole file exists. */
    public static function deliver(Spreadsheet $ss, string $baseName, string $format): void
    {
        $csv   = $format === 'csv';
        $bytes = self::render($ss, $format);
        while (ob_get_level() > 0) {            // CI installs more than one output buffer
            ob_end_clean();
        }
        header('Content-Type: ' . ($csv ? 'text/csv; charset=UTF-8' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));
        header('Content-Disposition: attachment; filename="' . $baseName . ($csv ? '.csv' : '.xlsx') . '"');
        header('Cache-Control: max-age=0');
        header('Content-Length: ' . strlen($bytes));
        echo $bytes;
        exit;
    }

    // ------------------------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------------------------

    /** Plain text of a grid label: no markup, no entities, no bullet/arrow markers, valid UTF-8 preserved. */
    public static function cleanLabel(string $raw): string
    {
        if ($raw === '') { return ''; }
        $s = str_replace(['<sub><em>', '</em></sub>'], [' ', ''], $raw);
        $s = strip_tags($s);
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $s = str_replace("\xC2\xA0", ' ', $s);                                   // NBSP (U+00A0) -> space
        $s = preg_replace('/^[\s»›▣▢□■▪▫◻◼▬▸▷▶▴▵‣⁃]+/u', '', $s) ?? $s;          // leading hierarchy markers only
        $s = preg_replace('/\s{2,}/u', ' ', $s) ?? $s;
        return trim($s);
    }

    private static function indent(string $type): int
    {
        return match ($type) { 'grp' => 1, 'acc', 'inv' => 2, default => 0 };
    }

    private static function start(array $opt): array
    {
        $ss = new Spreadsheet();
        $sh = $ss->getActiveSheet();
        $sh->setTitle(substr((string)($opt['sheet'] ?? 'Report'), 0, 31));
        $sh->getPageSetup()->setOrientation('landscape');
        return [$ss, $sh, !empty($opt['csv']) ? self::CSV_FORMAT : self::XLSX_FORMAT];
    }

    private static function titleBlock(Worksheet $sh, array $opt, string $titleRange, string $subRange): void
    {
        $sh->mergeCells($titleRange);
        $sh->setCellValueExplicit(explode(':', $titleRange)[0], (string)($opt['title'] ?? ''), DataType::TYPE_STRING);
        $sh->getStyle(explode(':', $titleRange)[0])->applyFromArray([
            'font' => ['bold' => true, 'size' => 15], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sh->mergeCells($subRange);
        $sh->setCellValueExplicit(explode(':', $subRange)[0], (string)($opt['subtitle'] ?? ''), DataType::TYPE_STRING);
        $sh->getStyle(explode(':', $subRange)[0])->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '595656']], 'alignment' => ['horizontal' => 'center'],
        ]);
    }

    private static function putNumber(Worksheet $sh, string $cell, $value, string $format, bool $always = false): void
    {
        if ($value === null || $value === '') {
            if (!$always) { return; }                                             // blank stays blank
            $value = 0.0;
        }
        $sh->setCellValueExplicit($cell, round((float)$value, 2), DataType::TYPE_NUMERIC);
        $sh->getStyle($cell)->getNumberFormat()->setFormatCode($format);
        $sh->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    private static function headerStyle(Worksheet $sh, string $range): void
    {
        $sh->getStyle($range)->getFont()->setBold(true)->setSize(12);
        self::fill($sh, $range, 'F2F2F2');
        $sh->getStyle($range)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
    }

    private static function totalStyle(Worksheet $sh, string $range): void
    {
        $sh->getStyle($range)->getFont()->setBold(true);
        self::fill($sh, $range, 'F2F2F2');
        $sh->getStyle($range)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        $sh->getStyle($range)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);
    }

    private static function fill(Worksheet $sh, string $range, string $rgb): void
    {
        $sh->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($rgb);
    }

    /**
     * Notes taken from the ledger (why the sheet does not tally, facts that explain a line), written under the report.
     * Each note is ['level' => 'warn'|'info', 'text' => string]; a plain string is treated as 'warn'.
     */
    private static function notes(Worksheet $sh, array $notes, int $row, string $lastCol): void
    {
        $list = [];
        foreach ($notes as $n) {
            $n = is_array($n) ? $n : ['level' => 'warn', 'text' => (string)$n];
            if ((string)($n['text'] ?? '') !== '') { $list[] = $n; }
        }
        $first = true;
        foreach ($list as $i => $n) {
            $cell = 'A' . ($row + $i);
            $sh->mergeCells($cell . ':' . $lastCol . ($row + $i));
            $sh->setCellValueExplicit($cell, (string)$n['text'], DataType::TYPE_STRING);
            $warn = ($n['level'] ?? 'warn') === 'warn';
            $sh->getStyle($cell)->getFont()->setItalic(true)->setBold($warn && $first)->getColor()->setRGB($warn && $first ? 'C00000' : '595656');
            $sh->getStyle($cell)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
            $sh->getRowDimension($row + $i)->setRowHeight(max(15, 15 * (int)ceil(strlen((string)$n['text']) / 95)));
            if ($warn) { $first = false; }
        }
    }

    private static function widths(Worksheet $sh, array $cols, array $nameCols): void
    {
        foreach ($cols as $c) {
            $sh->getColumnDimension($c)->setWidth(in_array($c, $nameCols, true) ? 46 : 18);
        }
    }
}
