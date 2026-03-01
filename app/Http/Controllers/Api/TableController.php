<?php

namespace App\Http\Controllers\Api;

use App\Services\TableService;
use Illuminate\Http\Request;

class TableController
{
    public function __construct(private TableService $service) {}

    public function index(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        return response()->json($this->service->getAll($branchId));
    }

    public function updateStatus(Request $request, $id)
    {
        $isAvailable = $request->boolean('is_available');
        return response()->json($this->service->updateStatus((int)$id, $isAvailable));
    }
}
