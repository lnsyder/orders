<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ProductRequest
{
    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Type(type: 'string', message: 'Name must be a string')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Name must be at least {{ limit }} characters', maxMessage: 'Name cannot be longer than {{ limit }} characters')]
    public string $name;

    #[Assert\NotBlank(message: 'Category is required')]
    #[Assert\Type(type: 'integer', message: 'Category must be an integer')]
    #[Assert\GreaterThan(value: 0, message: 'Category must be greater than 0')]
    public int $category;

    #[Assert\NotBlank(message: 'Price is required')]
    #[Assert\Type(type: 'numeric', message: 'Price must be a number')]
    #[Assert\GreaterThan(value: 0, message: 'Price must be greater than 0')]
    public float $price;

    #[Assert\NotBlank(message: 'Stock is required')]
    #[Assert\Type(type: 'integer', message: 'Stock must be an integer')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'Stock cannot be negative')]
    public int $stock;

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->name = $data['name'] ?? $data['description'] ?? '';
        $request->category = $data['category'] ?? 0;
        $request->price = (float)($data['price'] ?? 0);
        $request->stock = (int)($data['stock'] ?? 0);
        return $request;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'price' => $this->price,
            'stock' => $this->stock
        ];
    }
}