<?php

namespace App\Service\Discount\Strategy;

use App\Entity\Order;
use App\Entity\OrderDiscount;
use App\Entity\Discount;
use App\Repository\DiscountRepository;
use App\Service\Discount\DiscountStrategyInterface;

class TenPercentOverThousandStrategy implements DiscountStrategyInterface
{
    private DiscountRepository $discountRepository;

    /**
     * @param DiscountRepository $discountRepository
     */
    public function __construct(DiscountRepository $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }

    /**
     * @param Order $order
     * @return OrderDiscount|null
     */
    public function calculate(Order $order): ?OrderDiscount
    {
        if ($order->getTotal() >= 1000) {
            $discountAmount = $order->getTotal() * 0.10;

            $discount = $this->getDiscountEntity();

            if (!$discount) {
                return null;
            }

            return new OrderDiscount(
                $order,
                $discount,
                '10_PERCENT_OVER_1000',
                $discountAmount,
                $order->getTotal() - $discountAmount
            );
        }

        return null;
    }

    /**
     * @return Discount|null
     */
    private function getDiscountEntity(): ?Discount
    {
        return $this->discountRepository->findOneBy(["reason" => "TenPercentOverThousandStrategy"]);
    }
}


