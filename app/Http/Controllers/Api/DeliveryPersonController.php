<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\DeliveryPersonRequest;
use App\Repository\Interface\DeliveryPersonRepositoryInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Throwable;

class DeliveryPersonController
{
    use ApiResponseTrait;

    public function __construct(
        private DeliveryPersonRepositoryInterface $repository
    ) {}

    /**
     * Get all delivery persons.
     */
    public function index()
    {
        try {
            $persons = $this->repository->all();
            return $this->successResponse($persons);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب قائمة الطيارين');
        }
    }

    /**
     * Store a new delivery person.
     */
    public function store(DeliveryPersonRequest $request)
    {
        try {
            $data = $request->validated();
            $data['branch_id'] = $request->user()->branch_id ?? 1;
            $data['status'] = 'offline';
            $data['is_active'] = $data['is_active'] ?? true;

            $person = $this->repository->create($data);

            return $this->successResponse($person, 'تم إضافة الطيار بنجاح', 201);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل إضافة الطيار');
        }
    }

    /**
     * Update an existing delivery person.
     */
    public function update(DeliveryPersonRequest $request, int $id)
    {
        try {
            $person = $this->repository->find($id);
            if (!$person) {
                return $this->errorResponse('الطيار غير موجود', 404);
            }

            $updatedPerson = $this->repository->update($id, $request->validated());

            return $this->successResponse($updatedPerson, 'تم تحديث بيانات الطيار بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل تحديث بيانات الطيار');
        }
    }

    /**
     * Delete a delivery person.
     */
    public function destroy(int $id)
    {
        try {
            $success = $this->repository->delete($id);
            if (!$success) {
                return $this->errorResponse('الطيار غير موجود أو لا يمكن حذفه', 404);
            }
            return $this->successResponse(null, 'تم حذف الطيار بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل حذف الطيار');
        }
    }
}
