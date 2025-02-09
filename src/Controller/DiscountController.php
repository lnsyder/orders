<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\Discount\DiscountService;
use App\Service\Order\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/discount')]
class DiscountController extends AbstractController
{
    /**
     * @param DiscountService $discountService
     * @param OrderService $orderService
     */
    public function __construct(
        readonly private DiscountService $discountService,
        readonly private OrderService $orderService
    ) {}

    /**
     * @param int $orderId
     * @return JsonResponse
     */
    #[Route('/calculate/{orderId}', methods: ['GET'])]
    public function calculate(int $orderId): JsonResponse
    {
        try {
            $order = $this->orderService->getOrder($orderId);

            if (!$order && !$order instanceof Order) {
                return $this->json(['error' => 'Order not found'], 404);
            }

            $discounts = $this->discountService->calculateDiscounts($order);
            return $this->json($discounts);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}