<?php

namespace App\DTO\Response;

use App\Entity\Product;

readonly class ProductResponse
{
    public function __construct(
        public int    $id,
        public string $name,
        public int    $category,
        public float  $price,
        public int    $stock
    ) {}

    /**
     * @param Product $product
     * @return self
     */
    public static function fromEntity(Product $product): self
    {
        return new self(
            id: $product->getId(),
            name: $product->getName(),
            category: $product->getCategory(),
            price: $product->getPrice(),
            stock: $product->getStock()
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
            'category' => $this->category,
            'price' => $this->price,
            'stock' => $this->stock
        ];
    }
}