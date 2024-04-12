<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Repository\UserRepository;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/login')]
class LoginController extends AbstractController
{
    /**
     * Renders the login page and handles user authentication.
     *
     * @param UserRepository $userRepository The repository for accessing User entities.
     * @param Request $request The HTTP request.
     * @param SessionInterface $session The session interface for managing sessions.
     * @return Response The HTTP response containing the rendered page or an error response.
     */
    #[Route('/', name: 'login_index', methods: ['GET', 'POST'])]
    public function index(UserRepository $userRepository, Request $request, SessionInterface $session): Response
    {
        $routeSuccessLogin = 'app_user_index';

        $user = new User();
        $form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //récupère les données user dans la base de données par rapport à l'adresse email.
            $userResponse = $userRepository->findByEmail($user->getEmail());

            if ($userResponse) {
                if (password_verify($user->getPassword(), $userResponse[0]['password'])) {
                    //Création de la session de l'id user.
                    $userId = $userResponse[0]['id'];
                    $session->set('user_id', $userId);

                    return $this->redirectToRoute($routeSuccessLogin);
                } else {
                    return new JsonResponse(['error' => 'Password or Email is incorrect'], Response::HTTP_BAD_REQUEST);
                }
            } else {
                return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
        }

        return $this->render('login/index.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Logs the user out by invalidating the session and redirects to the login page.
     *
     * @param SessionInterface $session The session interface for managing sessions.
     * @return RedirectResponse The HTTP response redirecting to the login page.
     */
    #[Route('/logout', name: 'logout')]
    public function logout(SessionInterface $session): RedirectResponse
    {
        $session->invalidate();

        return $this->redirectToRoute('login_index', [], Response::HTTP_SEE_OTHER);
    }
}
