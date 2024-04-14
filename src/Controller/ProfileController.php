<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Category;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/{id}", name="app_profile")
     */
    #[Route('/profile/{id}', name: 'app_profile')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $entityManager->getRepository(User::class)->find($request->get('id'));

        if ($this->getUser()->isAdmin()) {
            return $this->render('profile/index.html.twig', [
                'user' => $user,
                'isCurrentUser' => $this->getUser()->getId() === $user->getId(),
                'users' => $entityManager->getRepository(User::class)->findAll(),
                'categories' => $entityManager->getRepository(Category::class)->findAll(),
                'boards' => $entityManager->getRepository(Board::class)->findAll(),
                'topics' => $entityManager->getRepository(Topic::class)->findAll(),
            ]);
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'isCurrentUser' => $this->getUser()->getId() === $user->getId(),
        ]);
    }

    /**
     * Edit user profile
     *
     * @Route("/profile/{id}/edit", name="app_profile_edit")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/profile/{id}/edit', name: 'app_profile_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $entityManager->getRepository(User::class)->find($request->get('id'));

        if (($this->getUser()->getId() !== $user->getId()) && !$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
        }

       // update the user
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        //save the image file
        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('profilePicture')->getData();

            if ($profilePicture) {
                $newFilename = uniqid().'.'.$profilePicture->guessExtension();

                try {
                    $profilePicture->move(
                        $this->getParameter('profile_picture_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $oldProfilePicture = $user->getProfilePicture();

                if ($oldProfilePicture && file_exists($this->getParameter('profile_picture_directory').'/'.$oldProfilePicture)) {
                    unlink($this->getParameter('profile_picture_directory').'/'.$oldProfilePicture);
                }

                $user->setProfilePicture($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
        }
        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Bans a user by adding the 'ROLE_BANNED' role to their profile.
     *
     * @Route('/profile/{id}/ban', name='app_profile_ban')
     * @param EntityManagerInterface $entityManager The entity manager
     * @param Request $request The request object
     * @return Response The response object
     */
    #[Route('/profile/{id}/ban', name: 'app_profile_ban')]
    public function ban(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $entityManager->getRepository(User::class)->find($request->get('id'));

        if ($this->getUser()->isAdmin()) {
            $user->addRole('ROLE_BANNED');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profile', ['id' => $this->getUser()->getId()]);
    }

    /**
     * Unban user profile
     *
     * @param EntityManagerInterface $entityManager The entity manager
     * @param Request $request The request object
     *
     * @return Response The response object
     *
     * @Route('/profile/{id}/unban', name='app_profile_unban')
     */
    #[Route('/profile/{id}/unban', name: 'app_profile_unban')]
    public function unban(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $entityManager->getRepository(User::class)->find($request->get('id'));

        if ($this->getUser()->isAdmin()) {
            $user->removeRole('ROLE_BANNED');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profile', ['id' => $this->getUser()->getId()]);
    }
}
