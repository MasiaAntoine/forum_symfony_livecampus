<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\AuthService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    /**
     * Renders the index page for categories.
     *
     * @param CategoryRepository $categoryRepository The repository for accessing Category entities.
     * @param AuthService $auth The authentication service.
     * @param Request $request The HTTP request.
     * @return Response The HTTP response containing the rendered page.
     */
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository, AuthService $auth, Request $request): Response
    {
        if(!$auth->isConnected($request)) {
            return $this->redirectToRoute('login_index');
        }

        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * Handles the creation of a new category.
     *
     * @param Request $request The HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @param UserRepository $userRepository The repository for accessing User entities.
     * @return Response The HTTP response containing the rendered page or a redirection.
     */
    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->find($request->getSession()->get('user_id'));
            $category->setUser($user);
            $category->setCreatedAt(new \DateTime());
            $category->setUpdatedAt(new \DateTime());
    
            $entityManager->persist($category);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
    
    /**
     * Renders the show page for a specific category.
     *
     * @param Category $category The category entity to display.
     * @return Response The HTTP response containing the rendered page.
     */
    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * Handles the editing of a category.
     *
     * @param Request $request The HTTP request.
     * @param Category $category The category entity to edit.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @return Response The HTTP response containing the rendered page or a redirection.
     */
    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * Handles the deletion of a category.
     *
     * @param Request $request The HTTP request.
     * @param Category $category The category entity to delete.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @return Response The HTTP response containing a redirection.
     */
    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
