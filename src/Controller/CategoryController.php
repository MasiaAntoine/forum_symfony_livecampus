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
    /**
     * @Route("/category/{id}", name="app_category")
     */
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

    /**
     * @Route("/category/{id}/delete", name="app_category_delete")
     */
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

    /**
     * Edit a category.
     *
     * This route handles the editing of a category. It checks if the user is logged in and if not, it redirects to the login page.
     * It retrieves the category entity from the database using the provided category ID. If the category doesn't belong to the current user
     * and the user is not an admin, it redirects to the home page.
     * It creates a form using the CategoryType form type and binds it to the category entity.
     * If the form is submitted and valid, it updates the category in the database and redirects to the category page.
     * Otherwise, it renders the edit.html.twig template, passing the form, current user, and category to the view.
     *
     * @param EntityManagerInterface $entityManager The entity manager.
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
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
