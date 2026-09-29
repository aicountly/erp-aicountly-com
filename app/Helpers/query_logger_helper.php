<?php

if (! function_exists('log_query_to_db'))
{
    /**
     * Inserts one query log into the `query_logs` table.
     *
     * @param string $uri       The request URI
     * @param string $sql       The SQL string
     * @param float  $time      Execution time in seconds
     * @return void
     */
    function log_query_to_db(string $uri, string $sql, float $time): void
    {
		$model = new \App\Models\Admin\TransactionModel();
        $model->SaveServerQueryLog($uri,$sql,$time);       
    }
}
