<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Category;
use App\Entity\Topic;
use App\Form\BoardType;
use App\Form\CategoryType;
use App\Form\TopicType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BoardController extends AbstractController
{
    #[Route('/board/{id}', name: 'app_board')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $board = $entityManager->getRepository(Board::class)->find($request->get('id'));

        $topics = $board->getTopics();

        if (!$this->getUser()->isAdmin()) {
            foreach ($topics as $key => $topic) {
                if (empty(array_intersect($topic->getRolesAllowed(), $this->getUser()->getRoles())) && $topic->getAuthor() !== $this->getUser()) {
                    unset($topics[$key]);
                }
            }
        }

        $newTopic = new Topic();

        $form = $this->createForm(TopicType::class, $newTopic);

        $form->handleRequest($request);

        $newTopic->setAuthor($this->getUser());
        $newTopic->setBoard($board);

        $date = new \DateTime();
        $newTopic->setCreatedAt($date->format('Y-m-d H:i:s'));

        if ($form->isSubmitted() && $form->isValid() && !$this->getUser()->isBanned()) {
            $entityManager->persist($newTopic);
            $entityManager->flush();

            return $this->redirectToRoute('app_board', ['id' => $board->getId()]);
        }

        return $this->render('board/index.html.twig', [
            'board' => $board,
            'topics' => $topics,
            'form' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/board/{id}/delete', name: 'app_board_delete')]
    public function delete(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $board = $entityManager->getRepository(Board::class)->find($request->get('id'));
        $category = $board->getCategory();

        if (($board->getAuthor() === $this->getUser()) || $this->getUser()->isAdmin()) {
            $entityManager->remove($board);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_category', ['id' => $category->getId()]);
    }

    #[Route('/board/{id}/edit', name: 'app_board_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $board = $entityManager->getRepository(Board::class)->find($request->get('id'));

        if (($board->getAuthor() !== $this->getUser()) && !$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(BoardType::class, $board);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_board', ['id' => $board->getId()]);
        }

        return $this->render('board/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'board' => $board,
        ]);
    }
}
