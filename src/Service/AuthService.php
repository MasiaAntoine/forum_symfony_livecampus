<?php
namespace App\Service;

use App\Repository\UserRepository;
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

    public function isAdmin(Request $request, UserRepository $userRepository) : bool
    {
        $session = $request->getSession();

        if ($this->isConnected($request)) {
            $userId = $session->get('user_id');
            $role = $userRepository->findRoleById($userId);
            if ($role === "Admin") {
                return true;
            }
        }
        return false;
    }
}