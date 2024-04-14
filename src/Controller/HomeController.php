<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $categories = $entityManager->getRepository(Category::class)->findAll();

        if (!$this->getUser()->isAdmin()) {
            foreach ($categories as $key => $category) {
                if (empty(array_intersect($category->getRolesAllowed(), $this->getUser()->getRoles())) && $category->getAuthor() !== $this->getUser()) {
                    unset($categories[$key]);
                }
            }
        }

        $newCategory = new Category();

        $form = $this->createForm(CategoryType::class, $newCategory);
        $form->handleRequest($request);

        //add author to the new category
        $newCategory->setAuthor($this->getUser());

        if ($form->isSubmitted() && $form->isValid() && !$this->getUser()->isBanned()) {
            $entityManager->persist($newCategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'user' => $this->getUser(),
            'form' => $form->createView(),
        ]);
    }
}