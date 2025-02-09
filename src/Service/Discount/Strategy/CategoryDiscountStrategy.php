<?php

namespace App\Service\Discount\Strategy;

use App\Entity\Order;
use App\Entity\OrderDiscount;
use App\Entity\OrderProduct;
use App\Entity\Discount;
use App\Repository\DiscountRepository;
use App\Service\Discount\DiscountStrategyInterface;

class CategoryDiscountStrategy implements DiscountStrategyInterface
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
        $category1Items = array_filter($order->getOrderProductsArray(), static function($item) {
            return $item->getProduct()->getCategory() === 1;
        });

        if (count($category1Items) >= 2) {
            $cheapestItem = $this->findCheapestItem($category1Items);
            $discountAmount = $cheapestItem->getTotal() * 0.20;

            $discount = $this->getDiscountEntity();

            if (!$discount) {
                return null;
            }

            return new OrderDiscount(
                $order,
                $discount,
                'CATEGORY_1_DISCOUNT',
                $discountAmount,
                $order->getTotal() - $discountAmount
            );
        }
        return null;
    }

    /**
     * @param array $items
     * @return OrderProduct
     */
    private function findCheapestItem(array $items): OrderProduct
    {
        return array_reduce($items, static function($carry, $item) {
            if (!$carry || $item->getUnitPrice() < $carry->getUnitPrice()) {
                return $item;
            }
            return $carry;
        });
    }

    /**
     * @return Discount|null
     */
    private function getDiscountEntity(): ?Discount
    {
        return $this->discountRepository->findOneBy(["reason" => "CategoryDiscountStrategy"]);
    }
}