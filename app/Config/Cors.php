<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    public array $default = [
        'allowedOrigins' => [
            'http://localhost:5173',
            'https://aicountly.github.io',
            'https://my.aicountly.com',
            'https://books.aicountly.com',
            'https://aicountly.com',
            'https://sandbox.aicountly.com',
            'https://erp.aicountly.in',
        ],

        'allowedHeaders' => [
            'Authorization',
            'Content-Type',
            'X-Requested-With',
            'Accept',
            'Origin',
        ],

        'exposedHeaders' => [
            // keep empty unless you truly need something exposed
        ],

        'allowedMethods' => [
            'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS',
        ],

        // IMPORTANT for token-in-header SPAs
        'supportsCredentials' => false,
    ];
}