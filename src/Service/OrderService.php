<?php

namespace App\Service;

use App\Command\CreateOrderCommand;
use App\Command\CreateOrderHandler;
use App\Entity\Order;
use App\Repository\OrderRepositoryInterface;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private CreateOrderHandler $createOrderHandler
    ) {}

    public function getOrder(int $id): ?Order
    {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            throw new \InvalidArgumentException('Order not found');
        }
        return $order;
    }

    public function getAllOrders(): array
    {
        return $this->orderRepository->findAll();
    }

    public function createOrder(array $data): Order
    {
        $command = new CreateOrderCommand($data);
        return $this->createOrderHandler->handle($command);
    }

    public function deleteOrder(int $id): void
    {
        $order = $this->getOrder($id);
        $this->orderRepository->delete($order);
    }
}