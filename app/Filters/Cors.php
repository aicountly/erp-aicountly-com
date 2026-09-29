<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Cors implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');
        $origin = $request->getHeaderLine('Origin');

        $allowedOrigins = [
           'http://localhost:5173',
            'https://erp.aicountly.com',
            'https://my.aicountly.com',
			'https://erp.aicountly.in',
			'https://sandbox.aicountly.com',
			'https://aicountly.com',
			'https://books.aicountly.com',
			'https://aicountly.github.io'
        ];

        if (in_array($origin, $allowedOrigins, true)) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
            $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN');
        }

        if ($request->getMethod() === 'options') {
            return $response->setStatusCode(204);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // headers already set in before()
    }
}