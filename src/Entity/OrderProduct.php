<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OrderProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private Product $product;

    #[ORM\Column]
    private int $quantity;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $unitPrice;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $total;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @return void
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return void
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return void
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getUnitPrice(): string
    {
        return $this->unitPrice;
    }

    /**
     * @param string $unitPrice
     * @return void
     */
    public function setUnitPrice(string $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @return string
     */
    public function getTotal(): string
    {
        return $this->total;
    }

    /**
     * @param string $total
     * @return void
     */
    public function setTotal(string $total): void
    {
        $this->total = $total;
    }

    /**
     * @return void
     */
    public function calculateTotal(): void
    {
        $this->setTotal($this->getQuantity() * $this->getUnitPrice());
    }
}