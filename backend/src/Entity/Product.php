<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\ProductPhoto;
use App\Entity\User;


#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('product:read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('product:read')]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Groups('product:read')]
    private ?string $sku = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('product:read')]
    private ?string $photo = null; // path or filename

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups('product:read')]
    private ?float $costPrice = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups('product:read')]
    private ?float $valuePrice = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Groups('product:read')]
    private ?float $soldPrice = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('product:read')]
    private ?Category $category = null;

    #[ORM\Column(type: 'integer')]
    #[Groups('product:read')]
    private ?int $stock = 1;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups('product:read')]
    private ?string $comment = null;

    #[ORM\Column(type: 'date')]
    #[Groups('product:read')]
    private ?\DateTimeInterface $purchaseDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups('product:read')]
    private ?\DateTimeInterface $soldDate = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups('product:read')]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductPhoto::class, cascade: ['persist', 'remove'])]
    private Collection $photos;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

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

    #[Groups('product:read')]
    public function getPhotos(): Collection

    {
        return $this->photos;
    }

    public function addPhoto(ProductPhoto $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setProduct($this);
        }
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;
        return $this;
    }

    public function setCostPrice(float $costPrice): self
    {
        $this->costPrice = $costPrice;
        return $this;
    }

    public function setValuePrice(float $valuePrice): self
    {
        $this->valuePrice = $valuePrice;
        return $this;
    }

    public function setSoldPrice(?float $soldPrice): self
    {
        $this->soldPrice = $soldPrice;
        return $this;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function setPurchaseDate(\DateTimeInterface $date): self
    {
        $this->purchaseDate = $date;
        return $this;
    }

    public function setSoldDate(?\DateTimeInterface $date): self
    {
        $this->soldDate = $date;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setPhoto(?string $filename): self
    {
        $this->photo = $filename;
        return $this;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getCostPrice(): ?float
    {
        return $this->costPrice;
    }

    public function getValuePrice(): ?float
    {
        return $this->valuePrice;
    }

    public function getSoldPrice(): ?float
    {
        return $this->soldPrice;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function getSoldDate(): ?\DateTimeInterface
    {
        return $this->soldDate;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    #[Groups('product:read')]
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function removePhoto(ProductPhoto $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            if ($photo->getProduct() === $this) {
                $photo->setProduct(null);
            }
        }
        return $this;
    }
}
