<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Customer $customer;

    #[ORM\OneToMany(
        targetEntity: OrderProduct::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    private Collection $orderProducts;

    #[ORM\OneToMany(
        targetEntity: OrderDiscount::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    private Collection $orderDiscounts;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $total = '0';

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private DateTime $createdAt;

    /**
     *
     */
    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
        $this->createdAt = new DateTime();
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
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    /**
     * @param OrderProduct $orderProduct
     * @return $this
     */
    public function addOrderProduct(OrderProduct $orderProduct): self
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->add($orderProduct);
            $orderProduct->setOrder($this);
        }
        return $this;
    }

    /**
     * @param OrderProduct $orderProduct
     * @return $this
     */
    public function removeOrderProduct(OrderProduct $orderProduct): self
    {
        $this->orderProducts->removeElement($orderProduct);
        return $this;
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
     * @return $this
     */
    public function setTotal(string $total): self
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return void
     */
    public function calculateTotal(): void
    {
        $total = '0';
        foreach ($this->orderProducts as $orderProduct) {
            $total = bcadd($total, $orderProduct->getTotal(), 2);
        }
        $this->total = $total;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param OrderProduct $orderProduct
     * @return bool
     */
    public function hasOrderProduct(OrderProduct $orderProduct): bool
    {
        return $this->orderProducts->contains($orderProduct);
    }

    /**
     * @return $this
     */
    public function clearOrderProducts(): self
    {
        $this->orderProducts->clear();
        return $this;
    }

    /**
     * @return array<OrderProduct>
     */
    public function getOrderProductsArray(): array
    {
        return $this->orderProducts->toArray();
    }

    /**
     * @return int
     */
    public function countOrderProducts(): int
    {
        return $this->orderProducts->count();
    }

    /**
     * @return Collection<int, OrderDiscount>
     */
    public function getOrderDiscounts(): Collection
    {
        return $this->orderDiscounts;
    }

    /**
     * @param OrderDiscount $orderDiscount
     * @return $this
     */
    public function addOrderDiscount(OrderDiscount $orderDiscount): self
    {
        if (!$this->orderDiscounts->contains($orderDiscount)) {
            $this->orderDiscounts->add($orderDiscount);
            $orderDiscount->setOrder($this);
        }

        return $this;
    }

    /**
     * @param OrderDiscount $orderDiscount
     * @return $this
     */
    public function removeOrderDiscount(OrderDiscount $orderDiscount): self
    {
        $this->orderDiscounts->removeElement($orderDiscount);

        return $this;
    }
}