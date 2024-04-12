<?php 
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class AuthService
{
    /**
     * Checks if a user is connected based on the presence of a 'user_id' key in the session.
     *
     * @param Request $request The current request object.
     *
     * @return bool True if a user is connected, false otherwise.
     */
    public function isConnected(Request $request) : bool
    {
        $session = $request->getSession();
        if (!$session->has('user_id')) {
            return false;
        }
        return true;
    }
}