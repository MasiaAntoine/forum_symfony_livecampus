<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Repository\UserRepository;
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
                if($userResponse[0]['password'] === $user->getPassword()) {
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

    #[Route('/logout', name: 'logout')]
    public function logout(SessionInterface $session): RedirectResponse
    {
        $session->invalidate();

        return $this->redirectToRoute('login_index', [], Response::HTTP_SEE_OTHER);
    }
}
