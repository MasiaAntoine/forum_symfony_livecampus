<?php 
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class AuthService
{
    public function isConnected(Request $request) : bool
    {
        $session = $request->getSession();
        if (!$session->has('user_id')) {
            return false;
        }
        return true;
    }
}