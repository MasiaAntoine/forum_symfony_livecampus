<?php

namespace App\Entity;

use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
class Topic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Board::class, inversedBy: 'topics')]
    #[ORM\JoinColumn(nullable: false)]
    private $board;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'topics')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    #[ORM\OneToMany(mappedBy: 'topic', targetEntity: Message::class)]
    private $messages;

    #[ORM\Column(type: 'json')]
    private array $rolesAllowed = [];

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getRolesAllowed(): array
    {
        return $this->rolesAllowed;
    }

    public function setRolesAllowed(array $rolesAllowed): static
    {
        $this->rolesAllowed = $rolesAllowed;

        return $this;
    }

    public function addRoleAllowed(string $rolesAllowed): static
    {
        if (!in_array($rolesAllowed, $this->rolesAllowed)) {
            $this->rolesAllowed[] = $rolesAllowed;
        }

        return $this;
    }

    public function removeRoleAllowed(string $rolesAllowed): static
    {
        if (in_array($rolesAllowed, $this->rolesAllowed)) {
            $this->rolesAllowed = array_diff($this->rolesAllowed, [$rolesAllowed]);
        }

        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getBoard(): ?Board
    {
        return $this->board;
    }

    public function setBoard(?Board $board): static
    {
        $this->board = $board;

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

    public function getMessages()
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setTopic($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getTopic() === $this) {
                $message->setTopic(null);
            }
        }

        return $this;
    }

    public function getUsers() : array
    {
        $users = [];
        foreach ($this->messages as $message) {
            if (!in_array($message->getAuthor(), $users))
                $users[] = $message->getAuthor();
        }
        return $users;
    }
}
