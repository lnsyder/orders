<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CustomerRequest
{
    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Type(type: 'string', message: 'Name must be a string')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Name must be at least {{ limit }} characters', maxMessage: 'Name cannot be longer than {{ limit }} characters')]
    public string $name;

    #[Assert\NotBlank(message: 'Since date is required')]
    #[Assert\Date(message: 'Invalid date format')]
    public string $since;

    #[Assert\NotBlank(message: 'Revenue is required')]
    #[Assert\Type(type: 'numeric', message: 'Revenue must be a number')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'Revenue cannot be negative')]
    public float $revenue;

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->name = $data['name'] ?? '';
        $request->since = $data['since'] ?? '';
        $request->revenue = (float)($data['revenue'] ?? 0);
        return $request;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'since' => $this->since,
            'revenue' => $this->revenue
        ];
    }
}