<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function find(int $id): ?Order
    {
        return $this->entityManager->getRepository(Order::class)->find($id);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Order::class)->findAll();
    }

    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public function delete(Order $order): void
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }
}