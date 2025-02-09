<?php

namespace App\Service\Discount;

use App\DTO\Response\DiscountResponse;
use App\Entity\Order;
use App\Entity\OrderDiscount;
use App\Repository\OrderDiscountRepository;
use App\Service\Discount\Chain\DiscountChain;
use App\Service\Discount\Factory\DiscountStrategyFactory;

readonly class DiscountService
{
    /**
     * @param DiscountChain $discountChain
     * @param DiscountStrategyFactory $strategyFactory
     * @param OrderDiscountRepository $orderDiscountRepository
     */
    public function __construct(
        private DiscountChain           $discountChain,
        private DiscountStrategyFactory $strategyFactory,
        private OrderDiscountRepository $orderDiscountRepository
    )
    {
    }

    public function calculateDiscounts(Order $order): DiscountResponse
    {
        $strategies = $this->strategyFactory->getStrategies();

        foreach ($strategies as $strategy) {
            $this->discountChain->addStrategy($strategy);
        }

        $results = $this->discountChain->calculate($order);

        $this->saveDiscounts($order, $results['discounts']);

        return DiscountResponse::fromArray($order, $results);
    }


    /**
     * @param Order $order
     * @param array $orderDiscounts
     */
    private function saveDiscounts(Order $order, array $orderDiscounts): void
    {
        $this->orderDiscountRepository->removeDiscountsByOrder($order);

        foreach ($orderDiscounts as $orderDiscount) {
            $orderDiscountEntity = new OrderDiscount(
                $order,
                $orderDiscount->getDiscount(),
                $orderDiscount->getDiscount()->getReason(),
                $orderDiscount->getDiscountAmount(),
                $orderDiscount->getSubtotal()
            );

            $this->orderDiscountRepository->save($orderDiscountEntity);
        }
    }
}