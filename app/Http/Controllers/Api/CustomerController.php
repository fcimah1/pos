<?php

namespace App\Http\Controllers\Api;

use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController
{
    public function __construct(private CustomerService $service) {}

    public function index(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        return response()->json($this->service->getAll($branchId));
    }

    public function search(Request $request)
    {
        $query = $request->query('q', '');
        $branchId = $request->user()?->branch_id ?? 1;
        return response()->json($this->service->search($query, $branchId));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['branch_id'] = $request->user()?->branch_id ?? 1;
        $customer = $this->service->create($data);
        return response()->json($customer, 201);
    }

    public function show(Request $request, $id)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        return response()->json($this->service->findById((int)$id, $branchId));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $data['branch_id'] = $request->user()?->branch_id ?? 1;
        $customer = $this->service->update((int)$id, $data);
        return response()->json($customer);
    }

    public function destroy(Request $request, $id)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        $this->service->delete((int)$id, $branchId);
        return response()->json(null, 204);
    }
}
