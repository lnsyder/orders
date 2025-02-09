<?php

namespace App\DTO\Response;

use App\Entity\Order;

readonly class OrderResponse
{
    public function __construct(
        public int    $id,
        public int    $customerId,
        public array  $items,
        public float  $total,
        public string $createdAt
    ) {}

    /**
     * @param Order $order
     * @return self
     */
    public static function fromEntity(Order $order): self
    {
        return new self(
            id: $order->getId(),
            customerId: $order->getCustomer()->getId(),
            items: array_map(static function($orderProduct) {
                return [
                    'product_id' => $orderProduct->getProduct()->getId(),
                    'name' => $orderProduct->getProduct()->getName(),
                    'quantity' => $orderProduct->getQuantity(),
                    'unit_price' => $orderProduct->getUnitPrice(),
                    'total' => $orderProduct->getTotal()
                ];
            }, $order->getOrderProducts()->toArray()),
            total: $order->getTotal(),
            createdAt: $order->getCreatedAt()->format('Y-m-d H:i:s')
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customerId,
            'items' => $this->items,
            'total' => $this->total,
            'createdAt' => $this->createdAt
        ];
    }
}