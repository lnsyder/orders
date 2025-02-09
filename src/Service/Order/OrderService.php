<?php

namespace App\Service\Order;

use App\DTO\Request\OrderRequest;
use App\DTO\Response\OrderListResponse;
use App\DTO\Response\OrderResponse;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

readonly class OrderService
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @param int $id
     * @return Order|null
     */
    public function getOrder(int $id): ?Order
    {
        return $this->entityManager->getRepository(Order::class)->find($id);
    }

    /**
     * @param int $id
     * @return OrderResponse
     */
    public function getOrderResponse(int $id): OrderResponse
    {
        $order = $this->getOrder($id);

        if (!$order) {
            throw new InvalidArgumentException('Order not found');
        }

        return OrderResponse::fromEntity($order);
    }

    /**
     * @return array
     */
    public function getAllOrders(): array
    {
        return $this->entityManager->getRepository(Order::class)->findAll();
    }

    /**
     * @return OrderListResponse
     */
    public function getAllOrdersResponse(): OrderListResponse
    {
        $orders = $this->getAllOrders();
        return OrderListResponse::fromOrders($orders);
    }

    /**
     * @param array $data
     * @return Order
     */
    public function saveOrder(array $data): Order
    {
        return $this->entityManager->wrapInTransaction(function () use ($data) {
            if(isset($data['id'])){
               $order = $this->getOrder($data['id']);
            }else{
                $order = new Order();
                $customer = $this->entityManager->getRepository(Customer::class)->find($data['customer_id']);
                if ($customer instanceof Customer) {
                    $order->setCustomer($customer);
                } else {
                    throw new InvalidArgumentException('Customer not found');
                }
            }

            if (isset($data['id']) && !$order) {
                throw new InvalidArgumentException('Order not found for update');
            }

            $existingOrderProducts = [];
            if (isset($data['id'])) {
                foreach ($order->getOrderProducts() as $existingOrderProduct) {
                    $existingOrderProducts[$existingOrderProduct->getProduct()->getId()] = $existingOrderProduct;
                }
            }

            $processedProducts = [];

            if (empty($data['items'])) {
                foreach ($existingOrderProducts as $existingOrderProduct) {
                    $order->removeOrderProduct($existingOrderProduct);
                    $this->entityManager->remove($existingOrderProduct);
                }
            } else {
                foreach ($data['items'] as $itemData) {
                    $product = $this->entityManager->getRepository(Product::class)->find($itemData['product_id']);
                    if (!$product) {
                        throw new InvalidArgumentException(
                            sprintf('Product with id %d not found', $itemData['product_id'])
                        );
                    }

                    $orderProduct = $existingOrderProducts[$product->getId()] ?? new OrderProduct();

                    $orderProduct->setOrder($order);
                    $orderProduct->setProduct($product);
                    $orderProduct->setQuantity($itemData['quantity']);
                    $orderProduct->setUnitPrice($product->getPrice());
                    $orderProduct->calculateTotal();

                    if (!isset($existingOrderProducts[$product->getId()])) {
                        $order->addOrderProduct($orderProduct);
                    }

                    $this->entityManager->persist($orderProduct);
                    $processedProducts[] = $product->getId();
                }

                foreach ($existingOrderProducts as $productId => $existingOrderProduct) {
                    if (!in_array($productId, $processedProducts, true)) {
                        $order->removeOrderProduct($existingOrderProduct);
                        $this->entityManager->remove($existingOrderProduct);
                    }
                }
            }

            $order->calculateTotal();
            $this->entityManager->persist($order);

            return $order;
        });
    }

    /**
     * @param OrderRequest $orderRequest
     * @return OrderResponse
     */
    public function saveOrderFromDTO(OrderRequest $orderRequest): OrderResponse
    {
        $order = $this->saveOrder($orderRequest->toArray());
        return OrderResponse::fromEntity($order);
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteOrder(int $id): void
    {
        $order = $this->getOrder($id);
        if (!$order) {
            throw new InvalidArgumentException('Order not found');
        }

        foreach ($order->getOrderProducts() as $orderProduct) {
            $this->entityManager->remove($orderProduct);
        }

        foreach ($order->getOrderDiscounts() as $orderDiscount) {
            $this->entityManager->remove($orderDiscount);
        }

        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }

}