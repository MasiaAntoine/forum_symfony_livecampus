<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Topic::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private $topic;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    #[ORM\OneToMany(targetEntity: Attachment::class, mappedBy: 'message')]
    private $attachments;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->attachments = new ArrayCollection();
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getAttachments()
    {
        return $this->attachments;
    }

    public function setAttachments($attachments): static
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function addAttachment($attachment): static
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    public function setContent($content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): static
    {
        $this->topic = $topic;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
