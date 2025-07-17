<?php

namespace App\Entity;

use App\Repository\RevenueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

#[ORM\Entity(repositoryClass: RevenueRepository::class)]
class Revenue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['revenue:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'revenues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['revenue:read'])]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['revenue:read'])]
    private ?float $amount = null;

    #[ORM\Column(length: 100)]
    #[Groups(['revenue:read'])]
    private ?string $category = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    #[Groups(['revenue:read'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['revenue:read'])]
    private ?string $paymentMethod = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['revenue:read'])]
    private ?string $customer = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['revenue:read'])]
    private ?string $invoiceNumber = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['revenue:read'])]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['revenue:read'])]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // ========== GETTERS AND SETTERS ==========

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(?string $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
