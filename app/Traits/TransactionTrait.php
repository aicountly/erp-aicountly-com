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
        }
    }
}