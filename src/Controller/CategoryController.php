<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Category;
use App\Form\BoardType;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'app_category')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $category = $entityManager->getRepository(Category::class)->find($request->get('id'));

        $boards = $category->getBoards();

        if (!$this->getUser()->isAdmin()) {
            foreach ($boards as $key => $board) {
                if (empty(array_intersect($board->getRolesAllowed(), $this->getUser()->getRoles())) && $board->getAuthor() !== $this->getUser()) {
                    unset($boards[$key]);
                }
            }
        }

        $newBoard = new Board();

        $form = $this->createForm(BoardType::class, $newBoard);
        $form->handleRequest($request);

        $newBoard->setAuthor($this->getUser());
        $newBoard->setCategory($category);

        if ($form->isSubmitted() && $form->isValid() && !$this->getUser()->isBanned()) {
            $entityManager->persist($newBoard);
            $entityManager->flush();

            return $this->redirectToRoute('app_category', ['id' => $category->getId()]);
        }

        return $this->render('category/index.html.twig', [
            'category' => $category,
            'boards' => $boards,
            'form' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/category/{id}/delete', name: 'app_category_delete')]
    public function delete(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $category = $entityManager->getRepository(Category::class)->find($request->get('id'));

        if (($category->getAuthor() === $this->getUser()) || $this->getUser()->isAdmin()) {
            if ($category->getBoards()->count() > 0) {
                $this->addFlash('error', 'You cannot delete a category with boards');
                return $this->redirectToRoute('app_category', ['id' => $category->getId()]);
            }

            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/category/{id}/edit', name: 'app_category_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request): Response
    {
        // use edit.html.twig
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $category = $entityManager->getRepository(Category::class)->find($request->get('id'));

        if (($category->getAuthor() !== $this->getUser()) && !$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_category', ['id' => $category->getId()]);
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'category' => $category,
        ]);
    }
}
