<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;   
use App\Services\OrderService;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders with customer and items.
     */
    public function index()
    {
        $orders = $this->orderService->listOrders();

        return OrderResource::collection($orders);
    }

    public function show(int $id)
    {
        $order = $this->orderService->getOrder($id);

        return OrderResource($order);
    }

    public function store(StoreOrderRequest $request)
    {
        // This is already validated & sanitised
        $data = $request->validated();

        $order = $this->orderService->createOrder($data);

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $id)
    {
        $data = $request->validated();
        $status = $data['status'];

        $order = $this->orderService->updateStatus($id, $status);

        return new OrderResource($order);
    }

    public function destroy(int $id)
    {
        $this->orderService->deleteOrder($id);

        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }
}
