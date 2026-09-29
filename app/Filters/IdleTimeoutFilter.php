<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class IdleTimeoutFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $timeout = 3600; // 60 minutes

        if ($session->has('last_activity')) {
            $lastActivity = $session->get('last_activity');

            if ((time() - $lastActivity) > $timeout) {
                $session->destroy();

                // ✅ AJAX request
                if ($request->isAJAX()) {
                    return service('response')
                        ->setStatusCode(401)
                        ->setJSON([
                            'status'  => 'session_expired',
                            'message' => 'Session expired. Please login again.'
                        ]);
                }

                // ✅ Normal browser request
                return redirect()->to('https://my.aicountly.com/login/logout');
            }
        }

        // Update activity ONLY if session is valid
        $session->set('last_activity', time());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}
