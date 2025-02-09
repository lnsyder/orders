<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private int $category;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $price;

    #[ORM\Column]
    private int $stock;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getCategory(): int
    {
        return $this->category;
    }

    /**
     * @param int $category
     * @return void
     */
    public function setCategory(int $category): void
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @param string $price
     * @return void
     */
    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     * @return void
     */
    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function increaseStock(int $quantity): self
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than 0');
        }

        $this->stock += $quantity;
        return $this;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function decreaseStock(int $quantity): self
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than 0');
        }

        if ($this->stock < $quantity) {
            throw new InvalidArgumentException(sprintf(
                'Not enough stock. Requested: %d, Available: %d',
                $quantity,
                $this->stock
            ));
        }

        $this->stock -= $quantity;
        return $this;
    }
}