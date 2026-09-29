<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
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

        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        }

        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With, Accept");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Expose-Headers: Set-Cookie");

        // IMPORTANT: Preflight response
        if ($request->getMethod() === 'options') {
            http_response_code(200);
            exit;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
