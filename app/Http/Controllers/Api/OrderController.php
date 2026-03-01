<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateOrderDTO;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\OrderStatusRequest;
use App\Services\OrderService;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\SettleOrdersRequest;
use Throwable;

class OrderController
{
    use ApiResponseTrait;

    public function __construct(
        private readonly \App\Repository\Interface\OrderRepositoryInterface $repository,
        private readonly OrderService $service
    ) {}

    public function store(OrderStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $data['branch_id'] = (int)($request->user()?->branch_id ?? 1);
            $data['user_id'] = (int)($request->user()?->id ?? 1);

            $dto = CreateOrderDTO::fromArray($data);
            $order = $this->service->create($dto);

            return $this->successResponse($order, 'تم إنشاء الطلب بنجاح', 201);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل إنشاء الطلب');
        }
    }

    public function index(Request $request)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            $filters = $request->only(['status', 'shift_id', 'customer_id']);

            $orders = $this->repository->getAll($branchId, $filters);
            return $this->successResponse($orders);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب الطلبات');
        }
    }

    public function suspended(Request $request)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            $orders = $this->repository->getSuspended($branchId);
            return $this->successResponse($orders);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب الطلبات المعلقة');
        }
    }

    public function show($id, Request $request)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            $order = $this->repository->findById($id, $branchId);
            return $this->successResponse($order);
        } catch (Throwable $e) {    
            return $this->handleException($e, 'الطلب غير موجود');
        }
    }

    public function update(OrderStoreRequest $request, $id)
    {
        try {
            Log::info('Updating order', ['id' => $id, 'data' => $request->all()]);

            $data = $request->validated();
            $data['branch_id'] = (int)($request->user()?->branch_id ?? 1);
            $data['user_id'] = (int)($request->user()?->id ?? 1);

            $dto = CreateOrderDTO::fromArray($data);
            $order = $this->repository->update($id, $dto);

            return $this->successResponse($order, 'تم تحديث الطلب بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل تحديث الطلب');
        }
    }

    public function updateStatus(OrderStatusRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $branchId = $request->user()?->branch_id ?? 1;

            $order = $this->repository->updateStatus($id, $validated['status'], $branchId);
            return $this->successResponse($order, 'تم تحديث حالة الطلب بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل تحديث حالة الطلب');
        }
    }

    public function settleOrders(SettleOrdersRequest $request)
    {
        try {
            $data = $request->validated();

            $this->service->settleDriverOrders(
                (int)$data['driver_id'],
                $data['order_ids']
            );

            return $this->successResponse(null, 'تم تسوية الطلبات بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل تسوية الطلبات');
        }
    }
}
