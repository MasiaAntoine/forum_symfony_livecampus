<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Board::class)]
    private $boards;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    #[ORM\Column(type: 'json')]
    private array $rolesAllowed = [];

    public function __construct()
    {
        $this->boards = new ArrayCollection();
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

    public function getBoards()
    {
        return $this->boards;
    }

    public function addBoard(Board $board): self
    {
        if (!$this->boards->contains($board)) {
            $this->boards[] = $board;
            $board->setCategory($this);
        }

        return $this;
    }

    public function removeBoard(Board $board): self
    {
        if ($this->boards->removeElement($board)) {
            if ($board->getCategory() === $this) {
                $board->setCategory(null);
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
