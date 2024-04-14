<?php

namespace App\Controller;

use App\Entity\Attachment;
use App\Entity\Message;
use App\Entity\Topic;
use App\Form\MessageType;
use App\Form\TopicType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TopicController extends AbstractController
{
    #[Route('/topic/{id}', name: 'app_topic')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $topic = $entityManager->getRepository(Topic::class)->find($request->get('id'));

        $messages = $topic->getMessages();

        $newMessage = new Message();

        $form = $this->createForm(MessageType::class, $newMessage);

        $form->handleRequest($request);

        $newMessage->setAuthor($this->getUser());
        $newMessage->setTopic($topic);

        $newMessage->setCreatedAt(new \DateTime());

        if ($form->isSubmitted() && $form->isValid() && !$this->getUser()->isBanned()) {
            $attachments = $form->get('attachments')->getData();

            foreach ($attachments as $attachment) {
                $newFileName = uniqid() . '-' . time() . '.' . $attachment->guessExtension();

                $attachment->move(
                    $this->getParameter('attachments_directory'),
                    $newFileName
                );

                $newAttachment = new Attachment();
                $newAttachment->setFileName($attachment->getClientOriginalName());
                $newAttachment->setFilePath($newFileName);
                $newAttachment->setMessage($newMessage);

                $entityManager->persist($newAttachment);
            }

            $entityManager->persist($newMessage);
            $entityManager->flush();

            return $this->redirectToRoute('app_topic', ['id' => $topic->getId()]);
        }


        return $this->render('topic/index.html.twig', [
            'topic' => $topic,
            'messages' => $messages,
            'form' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/topic/{id}/delete', name: 'app_topic_delete')]
    public function delete(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $topic = $entityManager->getRepository(Topic::class)->find($request->get('id'));

        if (($topic->getAuthor() === $this->getUser()) || $this->getUser()->isAdmin()) {
            if ($topic->getMessages()) {
                foreach ($topic->getMessages() as $message) {
                    if ($message->getAttachments()) {
                        foreach ($message->getAttachments() as $attachment) {
                            unlink($this->getParameter('attachments_directory') . '/' . $attachment->getFilePath());
                            $entityManager->remove($attachment);
                        }
                    }

                    $entityManager->remove($message);
                }
            }

            $entityManager->remove($topic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_board', ['id' => $topic->getBoard()->getId()]);
    }

    #[Route('/topic/{id}/edit', name: 'app_topic_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $topic = $entityManager->getRepository(Topic::class)->find($request->get('id'));

        if (($topic->getAuthor() !== $this->getUser()) && !$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(TopicType::class, $topic);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_topic', ['id' => $topic->getId()]);
        }

        return $this->render('topic/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'topic' => $topic,
        ]);
    }
}
