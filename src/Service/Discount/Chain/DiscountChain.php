<?php

namespace App\Service\Discount\Chain;

use App\Entity\Order;
use App\Service\Discount\DiscountStrategyInterface;

class DiscountChain
{
    /** @var DiscountStrategyInterface[] */
    private array $strategies = [];

    /**
     * @param DiscountStrategyInterface $strategy
     * @return void
     */
    public function addStrategy(DiscountStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    /**
     * @param Order $order
     * @return array
     */
    public function calculate(Order $order): array
    {
        $orderDiscounts = [];
        $totalDiscountAmount = 0;
        $total = $order->getTotal();

        foreach ($this->strategies as $strategy) {
            $discount = $strategy->calculate($order);
            if ($discount) {
                $orderDiscounts[] = $discount;

                $totalDiscountAmount += $discount->getDiscountAmount();
                $total = $discount->getSubtotal();
            }
        }

        return [
            'discounts' => $orderDiscounts,
            'total_discount' => number_format($totalDiscountAmount, 2),
            'discounted_total' => number_format($total, 2),
        ];
    }
}