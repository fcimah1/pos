<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController
{
    public function index(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        
        $customers = Customer::where('branch_id', $branchId)
            ->where('is_active', true)
            ->with('addresses')
            ->when($request->search, function ($query) {
                return $query->where('name', 'like', '%' . $query->request('search') . '%')
                    ->orWhere('phone', $query->request('search'));
            })
            ->paginate(50);

        return response()->json($customers);
    }

    public function search(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        $query = $request->get('q');

        if (!$query) {
            return response()->json([]);
        }

        $customers = Customer::where('branch_id', $branchId)
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('phone', 'like', '%' . $query . '%')
                  ->orWhereHas('addresses', function ($addr) use ($query) {
                      $addr->where('address_line_1', 'like', '%' . $query . '%')
                           ->orWhere('address_line_2', 'like', '%' . $query . '%');
                  });
            })
            ->with('addresses')
            ->limit(10)
            ->get();

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'notes' => 'nullable|string',
            'special_mark' => 'nullable|string|max:255',
            'addresses' => 'nullable|array',
            'addresses.*.address_line_1' => 'required_with:addresses|string',
            'addresses.*.address_line_2' => 'nullable|string',
            'addresses.*.type' => 'nullable|string|in:home,work,other',
        ]);

        $branchId = $request->user()?->branch_id ?? 1;

        $customer = Customer::create([
            'branch_id' => $branchId,
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'phone2' => $validated['phone2'] ?? null,
            'email' => $validated['email'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'special_mark' => $validated['special_mark'] ?? null,
            'is_active' => true,
        ]);

        // إنشاء العناوين
        if (!empty($validated['addresses'])) {
            foreach ($validated['addresses'] as $index => $addr) {
                $customer->addresses()->create([
                    'type' => $addr['type'] ?? 'home',
                    'address_line_1' => $addr['address_line_1'],
                    'address_line_2' => $addr['address_line_2'] ?? null,
                    'is_default' => $index === 0,
                ]);
            }
        }

        return response()->json($customer->load('addresses'), 201);
    }

    public function show($id, Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;
        
        $customer = Customer::where('branch_id', $branchId)
            ->with('addresses')
            ->findOrFail($id);

        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'email' => 'sometimes|nullable|email',
            'notes' => 'sometimes|nullable|string',
            'special_mark' => 'sometimes|nullable|string|max:255',
        ]);

        $branchId = $request->user()?->branch_id ?? 1;
        $customer = Customer::where('branch_id', $branchId)->findOrFail($id);
        
        $customer->update($validated);

        return response()->json($customer);
    }
}
