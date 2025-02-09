<?php

namespace App\Entity;

use App\Repository\DiscountRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountRepository::class)]
#[ORM\Table(name: 'discounts')]
class Discount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $reason;

    #[ORM\Column(type: 'string', length: 255)]
    private string $strategyClass;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $amount;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;
    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     * @return $this
     */
    public function setReason(string $reason): self
    {
        $this->reason = $reason;
        return $this;
    }

    /**
     * @return string
     */
    public function getStrategyClass(): string
    {
        return $this->strategyClass;
    }

    /**
     * @param string $strategyClass
     * @return $this
     */
    public function setStrategyClass(string $strategyClass): self
    {
        $this->strategyClass = $strategyClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return $this
     */
    public function setAmount(string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
