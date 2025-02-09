<?php

namespace App\Service\Discount\Strategy;

use App\Entity\Order;
use App\Entity\OrderDiscount;
use App\Entity\Discount;
use App\Repository\DiscountRepository;
use App\Service\Discount\DiscountStrategyInterface;

class BuyFiveGetOneStrategy implements DiscountStrategyInterface
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
        foreach ($order->getOrderProducts() as $item) {
            if ($item->getProduct()->getCategory() === 2 && $item->getQuantity() >= 6) {
                $freeItems = floor($item->getQuantity() / 6);
                $discountAmount = $item->getUnitPrice() * $freeItems;

                $discount = $this->getDiscountEntity();

                if (!$discount) {
                    return null;
                }

                return new OrderDiscount(
                    $order,
                    $discount,
                    'BUY_FIVE_GET_ONE',
                    $discountAmount,
                    $order->getTotal() - $discountAmount
                );
            }
        }

        return null;
    }

    /**
     * @return Discount|null
     */
    private function getDiscountEntity(): ?Discount
    {
        return $this->discountRepository->findOneBy(["reason" => "BuyFiveGetOneStrategy"]);
    }
}
