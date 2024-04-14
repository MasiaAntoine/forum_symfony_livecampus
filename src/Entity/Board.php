<?php

namespace App\Entity;

use App\Repository\BoardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoardRepository::class)]
class Board
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'boards')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\OneToMany(mappedBy: 'board', targetEntity: Topic::class)]
    private $topics;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'boards')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    #[ORM\Column(type: 'json')]
    private array $rolesAllowed = [];

    public function __construct()
    {
        $this->topics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getTopics()
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->setBoard($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getBoard() === $this) {
                $topic->setBoard(null);
            }
        }

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
}
