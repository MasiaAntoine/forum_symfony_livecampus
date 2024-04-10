<?php

namespace App\Controller;

use App\Entity\File;
use App\Form\FileType;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/file')]
class FileController extends AbstractController
{
    #[Route('/', name: 'app_file_index', methods: ['GET'])]
    public function index(FileRepository $fileRepository): Response
    {
        return $this->render('file/index.html.twig', [
            'files' => $fileRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_file_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $file = new File();
        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($file);
            $entityManager->flush();

            return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('file/new.html.twig', [
            'file' => $file,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_file_show', methods: ['GET'])]
    public function show(File $file): Response
    {
        return $this->render('file/show.html.twig', [
            'file' => $file,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_file_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, File $file, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('file/edit.html.twig', [
            'file' => $file,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_file_delete', methods: ['POST'])]
    public function delete(Request $request, File $file, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
    }
}
