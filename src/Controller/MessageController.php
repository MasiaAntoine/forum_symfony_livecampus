<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/message')]
class MessageController extends AbstractController
{
    /**
     * Renders the index page for messages.
     *
     * @param MessageRepository $messageRepository The repository for accessing Message entities.
     * @param AuthService $auth The authentication service.
     * @param Request $request The HTTP request.
     * @return Response The HTTP response containing the rendered page or a redirection.
     */
    #[Route('/', name: 'app_message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository, AuthService $auth, Request $request): Response
    {
        if(!$auth->isConnected($request)) {
            return $this->redirectToRoute('login_index');
        }

        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    /**
     * Handles the creation of a new message.
     *
     * @param Request $request The HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @return Response The HTTP response containing a redirection.
     */
    #[Route('/new', name: 'app_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    /**
     * Renders the show page for a specific message.
     *
     * @param Message $message The message entity to display.
     * @return Response The HTTP response containing the rendered page.
     */
    #[Route('/{id}', name: 'app_message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * Handles the editing of a message.
     *
     * @param Request $request The HTTP request.
     * @param Message $message The message entity to edit.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @return Response The HTTP response containing the rendered page or a redirection.
     */
    #[Route('/{id}/edit', name: 'app_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    /**
     * Handles the deletion of a message.
     *
     * @param Request $request The HTTP request.
     * @param Message $message The message entity to delete.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @return Response The HTTP response containing a redirection.
     */
    #[Route('/{id}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
    }
}
