<?php

namespace App\Service\Discount;

use App\Entity\Order;
use App\Entity\OrderDiscount;

interface DiscountStrategyInterface
{
    /**
     * @param Order $order
     * @return array|null
     */
    public function calculate(Order $order): ?OrderDiscount;
}