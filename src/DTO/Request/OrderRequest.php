<?php

namespace App\DTO\Request;

use AllowDynamicProperties;
use Symfony\Component\Validator\Constraints as Assert;

#[AllowDynamicProperties] class OrderRequest
{
    #[Assert\NotBlank(message: 'Customer ID is required')]
    #[Assert\Type(type: 'integer', message: 'Customer ID must be an integer')]
    public int $customerId;

    #[Assert\NotNull(message: 'Items array cannot be null')]
    #[Assert\Type(type: 'array', message: 'Items must be an array')]
    #[Assert\Count(min: 0, minMessage: 'At least one item is required')]
    #[Assert\All([
        new Assert\Collection([
            'fields' => [
                'product_id' => [
                    new Assert\NotBlank(message: 'Product ID is required'),
                    new Assert\Type(type: 'integer', message: 'Product ID must be an integer')
                ],
                'quantity' => [
                    new Assert\NotBlank(message: 'Quantity is required'),
                    new Assert\Type(type: 'integer', message: 'Quantity must be an integer'),
                    new Assert\GreaterThan(value: 0, message: 'Quantity must be greater than 0')
                ]
            ],
            'allowExtraFields' => false
        ])
    ])]
    public array $items = [];

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $request = new self();
        if (isset($data['id'])) {
            $request->id = $data['id'];
        }
        $request->customerId = $data['customerId'] ?? 0;
        $request->items = array_map(static function ($item) {
            return [
                'product_id' => $item['productId'] ?? 0,
                'quantity' => $item['quantity'] ?? 0
            ];
        }, $data['items'] ?? []);
        return $request;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'customer_id' => $this->customerId,
            'items' => $this->items
        ];
        if (isset($this->id)) {
            $array['id'] = $this->id;
        }
        return $array;
    }
}