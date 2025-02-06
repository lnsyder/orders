<?php

namespace App\Controller;

use App\Service\DiscountCalculatorService;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/discounts')]
class DiscountController extends AbstractController
{
    public function __construct(
        private DiscountCalculatorService $discountService,
        private OrderService $orderService
    ) {}

    #[Route('/calculate/{orderId}', methods: ['GET'])]
    public function calculate(int $orderId): JsonResponse
    {
        try {
            $order = $this->orderService->getOrder($orderId);
            $discounts = $this->discountService->calculateDiscounts($order);
            return $this->json($discounts);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}