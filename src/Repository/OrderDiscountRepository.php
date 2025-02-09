<?php

namespace App\Repository;

use App\Entity\Discount;
use App\Entity\Order;
use App\Entity\OrderDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderDiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderDiscount::class);
    }

    /**
     * @param OrderDiscount $orderDiscount
     * @return void
     */
    public function save(OrderDiscount $orderDiscount): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($orderDiscount);
        $entityManager->flush();
    }

    /**
     * @param Order $order
     */
    public function removeDiscountsByOrder(Order $order): void
    {
        $existingDiscounts = $this->findBy(['order' => $order]);
        $entityManager = $this->getEntityManager();

        foreach ($existingDiscounts as $existingDiscount) {
            $entityManager->remove($existingDiscount);
        }

        $entityManager->flush();
    }
}