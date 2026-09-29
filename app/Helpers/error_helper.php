<?php
// In app/Helpers/error_helper.php

if (!function_exists('formatDbException')) {
    function formatDbException(\Throwable $e): array
    {
        $trace = $e->getTrace();
        
        // Find actual error location (skip system files)
        $location = ['file' => 'Unknown', 'line' => 'Unknown', 'function' => 'Unknown'];
        
        foreach ($trace as $item) {
            if (isset($item['file']) && 
                strpos($item['file'], '/system/') === false && 
                strpos($item['file'], '/vendor/') === false) {
                $location = [
                    'file' => basename($item['file']),
                    'full_path' => $item['file'],
                    'line' => $item['line'] ?? 'N/A',
                    'function' => ($item['class'] ?? '') . ($item['type'] ?? '') . ($item['function'] ??  ''),
                ];
                break;
            }
        }
        
        return [
            'status' => false,
            'message' => html_entity_decode($e->getMessage()),
            'location' => $location,
        ];
    }
}