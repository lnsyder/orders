<?php

namespace App\DTO\Response;

use App\Entity\Order;

class DiscountResponse
{
    /**
     * @param int $orderId
     * @param array $discounts
     * @param string $totalDiscount
     * @param string $discountedTotal
     */
    public function __construct(
        public int    $orderId,
        public array  $discounts,
        public string $totalDiscount,
        public string $discountedTotal
    )
    {
    }


    /**
     * @param Order $order
     * @param array $discounts
     * @return self
     */
    public static function fromArray(Order $order, array $discounts): self
    {
        return new self(
            orderId: $order->getId(),
            discounts: array_map(static fn($discount) => [
                "discountReason" => $discount->getDiscountReason(),
                "discountAmount" => $discount->getDiscountAmount(),
                "subtotal" => $discount->getSubtotal()
            ], $discounts["discounts"]),
            totalDiscount: number_format($discounts["total_discount"], 2),
            discountedTotal: number_format($discounts["discounted_total"], 2)
        );
    }
}