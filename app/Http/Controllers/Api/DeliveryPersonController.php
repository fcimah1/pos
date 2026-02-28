<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\DeliveryPersonRequest;
use App\Models\DeliveryPerson;
use Illuminate\Http\Request;

class DeliveryPersonController
{
    public function index(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        $items = DeliveryPerson::where('branch_id', $branchId)
            ->where('is_active', true)
            ->paginate(50);

        return response()->json($items);
    }

    public function store(DeliveryPersonRequest $request)
    {
        $validated = $request->validated();

        $branchId = $request->user()?->branch_id ?? 1;
        $dp = DeliveryPerson::create(array_merge($validated, ['branch_id' => $branchId]));

        return response()->json($dp, 201);
    }

    public function update(DeliveryPersonRequest $request, $id)
    {
        $dp = DeliveryPerson::where('branch_id', $request->user()?->branch_id ?? 1)->findOrFail($id);

        $validated = $request->validated();

        $dp->update($validated);

        return response()->json($dp);
    }
}
