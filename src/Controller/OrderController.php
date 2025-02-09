<?php

namespace App\Controller;

use App\DTO\Request\OrderRequest;
use App\Request\RequestValidator;
use App\Service\Order\OrderService;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/order')]
class OrderController extends AbstractController
{
    /**
     * @param OrderService $orderService
     * @param RequestValidator $validator
     */
    public function __construct(
        readonly private OrderService     $orderService,
        readonly private RequestValidator $validator
    )
    {
    }

    /**
     * @return JsonResponse
     */
    #[Route('/list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json($this->orderService->getAllOrdersResponse());
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            return $this->json($this->orderService->getOrderResponse($id));
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/save', methods: ['POST', 'PUT'])]
    public function save(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $orderRequest = OrderRequest::fromArray($data);
            $this->validator->validate($orderRequest);

            return $this->json($this->orderService->saveOrderFromDTO($orderRequest), 201);
        } catch (JsonException $e) {
            return $this->json(['error' => 'Invalid JSON format'], 400);
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->orderService->deleteOrder($id);
            return $this->json(['message' => 'Order successfully deleted']);
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            return $this->json([
                'error' => 'An error occurred while processing your request',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}