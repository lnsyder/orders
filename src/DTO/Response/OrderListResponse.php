<?php

namespace App\DTO\Response;

readonly class OrderListResponse
{
    /**
     * @param OrderResponse[] $orders
     */
    public function __construct(
        public array $orders
    ) {}

    /**
     * @param array $orders
     * @return self
     */
    public static function fromOrders(array $orders): self
    {
        return new self(
            orders: array_map(static fn($order) => OrderResponse::fromEntity($order), $orders)
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static fn($order) => $order->toArray(), $this->orders);
    }
}