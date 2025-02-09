<?php

namespace App\DTO\Response;

use App\Entity\Customer;

readonly class CustomerResponse
{
    /**
     * @param int $id
     * @param string $name
     * @param string $since
     * @param float $revenue
     */
    public function __construct(
        public int    $id,
        public string $name,
        public string $since,
        public float  $revenue
    ) {}

    /**
     * @param Customer $customer
     * @return self
     */
    public static function fromEntity(Customer $customer): self
    {
        return new self(
            id: $customer->getId(),
            name: $customer->getName(),
            since: $customer->getSince()->format('Y-m-d'),
            revenue: $customer->getRevenue()
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'since' => $this->since,
            'revenue' => $this->revenue
        ];
    }
}