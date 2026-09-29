<?php
namespace App\Libraries;

/**
 * Request parameters of the Trial Balance / Profit & Loss / Balance Sheet, normalised in ONE place.
 *
 * The screen controller (Reportings) and the Excel exporter (Export) both call this, so a missing or
 * malformed parameter always resolves to the same default on screen and in the file. (They used to have
 * different defaults - e.g. nil_type 0 on screen, 1 in the export - which made the file differ from the page.)
 *
 * Dates are clamped to the open financial year exactly as before (validate_fy_from_date / validate_fy_to_date).
 */
class ReportParams
{
    /** @param array<string,mixed> $q  usually $_GET */
    public static function balanceSheet(array $q): array
    {
        return self::common($q, defaultView: 1, defaultNil: 1, withFormat: true);
    }

    public static function profitLoss(array $q): array
    {
        return self::common($q, defaultView: 1, defaultNil: 1, withFormat: true);
    }

    public static function trialBalance(array $q): array
    {
        $p = self::common($q, defaultView: 0, defaultNil: 0, withFormat: false);
        $p['view'] = in_array($p['view'], [0, 1, 2], true) ? $p['view'] : 0;
        return $p;
    }

    private static function common(array $q, int $defaultView, int $defaultNil, bool $withFormat): array
    {
        $view = self::intOr($q['view'] ?? null, $defaultView);
        if ($view < 0 || $view > 2) { $view = $defaultView; }

        $nil = self::intOr($q['nil_type'] ?? null, $defaultNil);
        $nil = $nil === 0 ? 0 : 1;

        $from = self::str($q['from_date'] ?? '');
        $to   = self::str($q['to_date'] ?? '');
        $from = validate_fy_from_date($from);   // d-m-Y, never before the FY start
        $to   = validate_fy_to_date($to);       // d-m-Y, never after the FY end

        $out = [
            'view'         => $view,
            'nil_type'     => $nil,
            'consolidated' => self::flag($q['consolidated'] ?? null),
            'from_date'    => $from,
            'to_date'      => $to,
            'from_ymd'     => date('Y-m-d', strtotime($from)),
            'to_ymd'       => date('Y-m-d', strtotime($to)),
            'export'       => strtolower(self::str($q['export'] ?? '')) === 'csv' ? 'csv' : 'excel',
        ];
        if ($withFormat) {
            $format = self::intOr($q['format'] ?? null, 1);
            $out['format'] = $format === 2 ? 2 : 1;
        }
        return $out;
    }

    /** Query string that reproduces exactly these parameters (used to build the export links). */
    public static function toQuery(array $p): string
    {
        $keys = ['view', 'nil_type', 'consolidated', 'from_date', 'to_date', 'format'];
        $out  = [];
        foreach ($keys as $k) {
            if (array_key_exists($k, $p)) { $out[$k] = $p[$k]; }
        }
        return http_build_query($out);
    }

    private static function str($v): string
    {
        return is_string($v) ? trim($v) : '';
    }

    private static function intOr($v, int $default): int
    {
        if (is_int($v)) { return $v; }
        if (is_string($v) && preg_match('/^\s*-?\d+\s*$/', $v)) { return (int)$v; }
        return $default;
    }

    private static function flag($v): int
    {
        if (is_string($v)) {
            return in_array(strtolower(trim($v)), ['1', 'true', 'on', 'yes'], true) ? 1 : 0;
        }
        return $v ? 1 : 0;
    }
}
