<?php

namespace App\Entity;

use App\Repository\OrderDiscountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderDiscountRepository::class)]
#[ORM\Table(name: 'order_discounts')]
class OrderDiscount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderDiscounts')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: Discount::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Discount $discount;

    #[ORM\Column(type: 'string', length: 255)]
    private string $discountReason;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $discountAmount;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $subtotal;

    public function __construct(Order $order, Discount $discount, string $discountReason, float $discountAmount, float $subtotal)
    {
        $this->order = $order;
        $this->discount = $discount;
        $this->discountReason = $discountReason;
        $this->discountAmount = $discountAmount;
        $this->subtotal = $subtotal;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
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
     * @return $this
     */
    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return Discount
     */
    public function getDiscount(): Discount
    {
        return $this->discount;
    }

    /**
     * @param Discount $discount
     * @return $this
     */
    public function setDiscount(Discount $discount): self
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return string
     */
    public function getDiscountReason(): string
    {
        return $this->discountReason;
    }

    /**
     * @param string $discountReason
     * @return $this
     */
    public function setDiscountReason(string $discountReason): self
    {
        $this->discountReason = $discountReason;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    /**
     * @param float $discountAmount
     * @return $this
     */
    public function setDiscountAmount(float $discountAmount): self
    {
        $this->discountAmount = $discountAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    /**
     * @param float $subtotal
     * @return $this
     */
    public function setSubtotal(float $subtotal): self
    {
        $this->subtotal = $subtotal;
        return $this;
    }
}
