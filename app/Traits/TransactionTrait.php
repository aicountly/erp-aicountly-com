<?php namespace App\Traits;

use Config\Database;

trait TransactionTrait
{
    protected function runTransaction(callable $callback)
    {
        $db = Database::connect();

        // IMPORTANT
        $db->transException(true);

        try {

            $db->transBegin();

            $result = $callback($db);

            if ($db->transStatus() === false) {

                throw new \Exception('Transaction status failed.');
            }

            // A composition-scheme voucher and its "GST PAID A/C" journal are one double entry, written
            // by two calculations that could disagree. Before this transaction is committed the entry is
            // completed from the rows it already carries (see CompositionPosting) and then checked: a
            // voucher that still does not balance is never committed, because every report built on the
            // ledger - Trial Balance, Balance Sheet, Profit & Loss - would be wrong by that difference.
            $this->settleCompositionPostings($db);

            $db->transCommit();

            return [
                'status'  => true,
                'message' => $result['message'] ?? 'Success',
                'result'  => $result['data'] ?? $result
            ];

        } catch (\Throwable $e) {

            $db->transRollback();

            return [
                'status'  => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'sql'     => $db->getLastQuery()
                    ? $db->getLastQuery()->getQuery()
                    : null,
                'trace'   => $e->getTraceAsString()
            ];
        } finally {

            \App\Libraries\CompositionPosting::forget();
        }
    }

    /**
     * Completes and verifies the composition-scheme entries this transaction wrote.
     *
     * Only vouchers the request itself touched are looked at, and of those only the ones in the
     * composition flow (a type-23 "GST PAID A/C" journal, or a voucher carrying a GST PAID leg): a
     * regular-registration branch writes no such rows, so nothing here changes what it saves. What the
     * stored rows cannot explain is left alone and logged rather than guessed at.
     *
     * @throws \Exception when a composition voucher would be committed one-sided
     */
    private function settleCompositionPostings($db): void
    {
        $ids = \App\Libraries\CompositionPosting::touchedIds();
        if (!$ids) { return; }

        $posting = new \App\Libraries\CompositionPosting($db);
        $plan    = $posting->plan($ids);

        if ($plan['actions']) {
            $posting->apply($plan['actions']);
            foreach ($plan['actions'] as $a) {
                $this->logTransactionNote('Composition posting completed (rule ' . $a['rule'] . '): voucher ' . $a['vch']
                    . ' account ' . $a['acc'] . ' ' . ((int)$a['side'] === 1 ? 'Dr ' : 'Cr ') . $a['amount']
                    . ' [' . $a['kind'] . '] - ' . $a['why']);
            }
        }

        if (!empty($plan['degraded'])) {
            // The company has no GST PAID A/C ledger or no bill-sundry master, so the entry cannot be
            // reasoned about here. Record it and let the save through: refusing every voucher of such a
            // company would be worse than the difference the audit already reports.
            foreach ($posting->unbalanced($ids) as $g) {
                $this->logTransactionNote('Voucher does not balance and the composition masters are incomplete: voucher(s) '
                    . implode(' + ', $g['group']) . ' difference ' . number_format($g['difference'], 2, '.', ''));
            }
            return;
        }

        $flow = [];
        foreach ($posting->unbalanced($ids) as $g) {
            $note = 'voucher(s) ' . implode(' + ', $g['group']) . ': debit - credit = ' . number_format($g['difference'], 2, '.', '');
            if ($posting->isCompositionFlow($g['group'])) {
                $flow[] = $note;
            } else {
                $this->logTransactionNote('Voucher does not balance (left exactly as the posting code wrote it): ' . $note);
            }
        }
        if ($flow) {
            throw new \Exception('This voucher cannot be saved because its entry does not balance: ' . implode('; ', $flow)
                . '. The GST of a composition-scheme voucher has to be posted to a tax ledger or to GST PAID A/C;'
                . ' please check the tax ledger set-up of this branch and save again.');
        }
    }

    /** Writes to the application's error log if that helper is loaded; never fails the transaction. */
    private function logTransactionNote(string $line): void
    {
        try {
            if (!function_exists('SaveErrorLog')) { helper('custom'); }
            if (function_exists('SaveErrorLog')) { SaveErrorLog($line); }
        } catch (\Throwable $e) {
            // logging is not worth losing a voucher over
        }
    }
}