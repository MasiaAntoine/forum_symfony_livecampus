<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SubjectController extends AbstractController
{
    #[Route('/subject', name: 'app_subject')]
    public function index(): Response
    {
        return $this->render('subject/index.html.twig', [
            'controller_name' => 'SubjectController',
        ]);
    }
}
