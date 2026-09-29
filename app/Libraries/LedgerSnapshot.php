<?php
namespace App\Libraries;

/**
 * Immutable-by-convention result of AccountingEngine::snapshot().
 *
 * Sign convention everywhere: a debit balance is POSITIVE, a credit balance is NEGATIVE.
 *
 * Per-account fields (see AccountingEngine::loadAccounts / classify):
 *   id, name, is_bsd, mapped, group_id, own_cat, cat, root_gid, chain_ok, missing_master,
 *   op         FY opening balance (accoppybal)
 *   pre        movement from FY start up to (not including) `from`
 *   dr, cr     movement inside [from, to]
 *   mv         dr - cr   (movement inside [from, to])
 *   cum        pre + mv  (movement FY start .. to)
 *   closing    op + pre + mv  (balance as on `to`)
 */
class LedgerSnapshot
{
    public string $from;
    public string $to;
    public string $fyStart;
    public string $fyEnd;
    public bool $consolidated;

    /** @var array<int,array<string,mixed>> */
    public array $accounts = [];
    /** @var array<int,array<string,mixed>> gid => group */
    public array $groups = [];
    /** @var array<int,int[]> parent gid => child gids */
    public array $children = [];
    /** @var array<int,int[]> gid => account ids whose immediate group is gid (and whose chain is intact) */
    public array $accByGroup = [];
    /** @var array<string,mixed> data-quality counters filled while loading */
    public array $quality = [
        'duplicate_mappings' => 0,
        'duplicate_groups'   => 0,
        'missing_masters'    => 0,
        'broken_chains'      => 0,
        'unmapped'           => 0,
    ];

    /** @var array<string,array<int,float>> */
    private array $sumCache = [];
    /** @var array<int,int[]> */
    private array $descCache = [];

    public function __construct(string $from, string $to, string $fyStart, string $fyEnd, bool $consolidated)
    {
        $this->from         = $from;
        $this->to           = $to;
        $this->fyStart      = $fyStart;
        $this->fyEnd        = $fyEnd;
        $this->consolidated = $consolidated;
    }

    /** Group ids of the group itself and every descendant (cycle safe). */
    public function descendants(int $gid): array
    {
        if (isset($this->descCache[$gid])) {
            return $this->descCache[$gid];
        }
        $out   = [];
        $stack = [$gid];
        $seen  = [];
        while ($stack) {
            $g = array_pop($stack);
            if (isset($seen[$g])) {
                continue;
            }
            $seen[$g] = true;
            $out[]    = $g;
            foreach ($this->children[$g] ?? [] as $c) {
                $stack[] = $c;
            }
        }
        return $this->descCache[$gid] = $out;
    }

    /** Ids of every account that sits anywhere below (or directly in) the group. */
    public function accountsUnder(int $gid): array
    {
        $ids = [];
        foreach ($this->descendants($gid) as $g) {
            foreach ($this->accByGroup[$g] ?? [] as $aid) {
                $ids[] = $aid;
            }
        }
        return $ids;
    }

    /** Sum of an account field over a group's whole subtree, rounded to 2 dp. */
    public function groupSum(int $gid, string $field): float
    {
        if (isset($this->sumCache[$field][$gid])) {
            return $this->sumCache[$field][$gid];
        }
        $sum = 0.0;
        foreach ($this->accountsUnder($gid) as $aid) {
            $sum += (float)$this->accounts[$aid][$field];
        }
        return $this->sumCache[$field][$gid] = round($sum, 2);
    }
}
