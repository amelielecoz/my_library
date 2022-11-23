<?php

namespace App\Entity;

use App\Repository\ReservationRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRequestRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ReservationRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $requestorName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'reservationRequests')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Book $book = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestorName(): ?string
    {
        return $this->requestorName;
    }

    public function setRequestorName(string $requestorName): self
    {
        $this->requestorName = $requestorName;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
