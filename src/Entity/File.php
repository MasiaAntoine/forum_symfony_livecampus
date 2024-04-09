<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'files')]
    #[ORM\JoinColumn(nullable: false)]
    private ?message $message_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessageId(): ?message
    {
        return $this->message_id;
    }

    public function setMessageId(?message $message_id): static
    {
        $this->message_id = $message_id;

        return $this;
    }
}
