<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Form\SubjectType;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/subject')]
class SubjectController extends AbstractController
{
    /**
     * Renders the index page for subjects.
     *
     * @param SubjectRepository $subjectRepository The repository for accessing Subject entities.
     * @param AuthService $auth The authentication service.
     * @param Request $request The HTTP request.
     * @return Response The HTTP response containing the rendered page or a redirection.
     */
    #[Route('/', name: 'app_subject_index', methods: ['GET'])]
    public function index(SubjectRepository $subjectRepository, AuthService $auth, Request $request): Response
    {
        if(!$auth->isConnected($request)) {
            return $this->redirectToRoute('login_index');
        }

        return $this->render('subject/index.html.twig', [
            'subjects' => $subjectRepository->findAll(),
        ]);
    }

    /**
     * Handles the creation of a new subject.
     *
     * @param Request $request The HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @param UserRepository $userRepository The repository for accessing User entities.
     * @return Response The HTTP response containing a redirection.
     */
    #[Route('/new', name: 'app_subject_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->find($request->getSession()->get('user_id'));
            $subject->setUser($user);
            $subject->setCreatedAt(new \DateTime());
            $subject->setUpdatedAt(new \DateTime());

            $entityManager->persist($subject);
            $entityManager->flush();

            return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subject/new.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }

    /**
     * Renders the show page for a specific subject.
     *
     * @param Subject $subject The subject entity to display.
     * @return Response The HTTP response containing the rendered page.
     */
    #[Route('/{id}', name: 'app_subject_show', methods: ['GET'])]
    public function show(Subject $subject): Response
    {
        return $this->render('subject/show.html.twig', [
            'subject' => $subject,
        ]);
    }

    /**
     * Handles the editing of a subject.
     *
     * @param Request $request The HTTP request.
     * @param Subject $subject The subject entity to edit.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @return Response The HTTP response containing the rendered page or a redirection.
     */
    #[Route('/{id}/edit', name: 'app_subject_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Subject $subject, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subject/edit.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }

    /**
     * Handles the deletion of a subject.
     *
     * @param Request $request The HTTP request.
     * @param Subject $subject The subject entity to delete.
     * @param EntityManagerInterface $entityManager The entity manager for database operations.
     * @return Response The HTTP response containing a redirection.
     */
    #[Route('/{id}', name: 'app_subject_delete', methods: ['POST'])]
    public function delete(Request $request, Subject $subject, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subject->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($subject);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
    }
}
