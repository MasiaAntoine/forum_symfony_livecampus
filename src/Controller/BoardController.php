<?php

namespace App\Controller;

use App\Entity\Board;
use App\Form\BoardType;
use App\Repository\BoardRepository;
use App\Repository\UserRepository;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/board')]
class BoardController extends AbstractController
{
    /**
     * Renders the index page for boards.
     *
     * @param BoardRepository $boardRepository The repository for accessing Board entities.
     * @param AuthService $auth The authentication service.
     * @param Request $request The HTTP request.
     * @return Response The HTTP response containing the rendered page.
     */
    #[Route('/', name: 'app_board_index', methods: ['GET'])]
    public function index(BoardRepository $boardRepository, AuthService $auth, Request $request): Response
    {
        if(!$auth->isConnected($request)) {
            return $this->redirectToRoute('login_index');
        }

        return $this->render('board/index.html.twig', [
            'boards' => $boardRepository->findAll(),
        ]);
    }

    /**
     * Handles the creation of a new board.
     *
     * @param Request $request The HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @param UserRepository $userRepository The repository for accessing User entities.
     * @return Response The HTTP response containing the rendered page or a redirection.
     */
    #[Route('/new', name: 'app_board_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $board = new Board();
        $form = $this->createForm(BoardType::class, $board);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->find($request->getSession()->get('user_id'));
            $board->setCreatedAt(new \DateTime());
            $board->setUpdatedAt(new \DateTime());
            $board->setUser($user);
            $entityManager->persist($board);
            $entityManager->flush();

            return $this->redirectToRoute('app_board_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('board/new.html.twig', [
            'board' => $board,
            'form' => $form,
        ]);
    }

    /**
     * Renders the show page for a specific board.
     *
     * @param Board $board The board entity to display.
     * @return Response The HTTP response containing the rendered page.
     */
    #[Route('/{id}', name: 'app_board_show', methods: ['GET'])]
    public function show(Board $board): Response
    {
        return $this->render('board/show.html.twig', [
            'board' => $board,
        ]);
    }

    /**
     * Handles the editing of a board.
     *
     * @param Request $request The HTTP request.
     * @param Board $board The board entity to edit.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @return Response The HTTP response containing the rendered page or a redirection.
     */
    #[Route('/{id}/edit', name: 'app_board_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Board $board, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BoardType::class, $board);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $board->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            return $this->redirectToRoute('app_board_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('board/edit.html.twig', [
            'board' => $board,
            'form' => $form,
        ]);
    }

    /**
     * Handles the deletion of a board.
     *
     * @param Request $request The HTTP request.
     * @param Board $board The board entity to delete.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @return Response The HTTP response containing a redirection.
     */
    #[Route('/{id}', name: 'app_board_delete', methods: ['POST'])]
    public function delete(Request $request, Board $board, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$board->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($board);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_board_index', [], Response::HTTP_SEE_OTHER);
    }
}
