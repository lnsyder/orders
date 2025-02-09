<?php

namespace App\Service\Discount\Factory;

use App\Repository\DiscountRepository;
use App\Service\Discount\DiscountStrategyInterface;
use RuntimeException;

class DiscountStrategyFactory
{
    /** @var DiscountStrategyInterface[] */
    private array $strategies = [];

    private DiscountRepository $discountRepository;

    /**
     * @param DiscountRepository $discountRepository
     */
    public function __construct(DiscountRepository $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }

    /**
     * @return void
     */
    public function loadStrategiesFromDatabase(): void
    {
        $discounts = $this->discountRepository->findAll();

        foreach ($discounts as $discount) {
            $this->addStrategy($discount->getId(), $this->createStrategyObject($discount));
        }
    }

    /**
     * @param $discount
     * @return DiscountStrategyInterface
     */
    private function createStrategyObject($discount): DiscountStrategyInterface
    {
        $strategyClass = $discount->getStrategyClass();

        if (!class_exists($strategyClass)) {
            throw new RuntimeException("Class $strategyClass does not exist!");
        }

        return new $strategyClass($this->discountRepository);
    }

    /**
     * @param string $type
     * @param DiscountStrategyInterface $strategy
     * @return void
     */
    public function addStrategy(string $type, DiscountStrategyInterface $strategy): void
    {
        $this->strategies[$type] = $strategy;
    }

    /**
     * @return DiscountStrategyInterface[]
     */
    public function getStrategies(): array
    {
        $this->loadStrategiesFromDatabase();
        return $this->strategies;
    }

    /**
     * @param string $type
     * @return DiscountStrategyInterface|null
     */
    public function createStrategy(string $type): ?DiscountStrategyInterface
    {
        return $this->strategies[$type] ?? null;
    }
}