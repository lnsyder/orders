<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $since;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $revenue;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getSince(): \DateTimeInterface
    {
        return $this->since;
    }

    /**
     * @param \DateTimeInterface $since
     * @return void
     */
    public function setSince(\DateTimeInterface $since): void
    {
        $this->since = $since;
    }

    /**
     * @return string
     */
    public function getRevenue(): string
    {
        return $this->revenue;
    }

    /**
     * @param string $revenue
     * @return void
     */
    public function setRevenue(string $revenue): void
    {
        $this->revenue = $revenue;
    }
}