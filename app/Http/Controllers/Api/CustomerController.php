<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomerController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private CustomerService $service) {}

    public function index(Request $request)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            return $this->successResponse($this->service->getAll($branchId, $request->all()));
        } catch (Throwable $e) {
            Log::error('Error in CustomerController@index: ', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->handleException($e, 'فشل جلب العملاء');
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->query('q', '');
            $branchId = $request->user()?->branch_id ?? 1;
            return $this->successResponse($this->service->search($query, $branchId));
        } catch (Throwable $e) {
            Log::error('Error in CustomerController@search: ', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->handleException($e, 'فشل البحث عن العميل');
        }
    }

    public function store(StoreCustomerRequest $request)
    {
        try {
            $data = $request->validated();
            $data['branch_id'] = $request->user()?->branch_id ?? 1;
            $customer = $this->service->create($data);
            return $this->successResponse($customer, 'تم إنشاء العميل بنجاح', 201);
        } catch (Throwable $e) {
            Log::error('Error in CustomerController@store: ', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->handleException($e, 'فشل إنشاء العميل');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            return $this->successResponse($this->service->findById((int)$id, $branchId));
        } catch (Throwable $e) {
            Log::error('Error in CustomerController@show: ', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->handleException($e, 'فشل جلب بيانات العميل');
        }
    }

    public function update(UpdateCustomerRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $data['branch_id'] = $request->user()?->branch_id ?? 1;
            $customer = $this->service->update((int)$id, $data);
            return $this->successResponse($customer, 'تم تحديث بيانات العميل بنجاح');
        } catch (Throwable $e) {
            Log::error('Error in CustomerController@update: ', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->handleException($e, 'فشل تحديث بيانات العميل');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            $this->service->delete((int)$id, $branchId);
            return $this->successResponse(null, 'تم حذف العميل بنجاح', 204);
        } catch (Throwable $e) {
            Log::error('Error in CustomerController@destroy: ', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->handleException($e, 'فشل حذف العميل');
        }
    }
}
