<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CustomerAddressRequest;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;

class CustomerAddressController
{
    public function store(CustomerAddressRequest $request)
    {
        $validated = $request->validated();

        $address = CustomerAddress::create($validated);

        if ($validated['is_default'] ?? false) {
            CustomerAddress::where('customer_id', $validated['customer_id'])
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        return response()->json($address, 201);
    }

    public function update(CustomerAddressRequest $request, $id)
    {
        $validated = $request->validated();

        $address = CustomerAddress::findOrFail($id);
        $address->update($validated);

        if ($validated['is_default'] ?? false) {
            CustomerAddress::where('customer_id', $address->customer_id)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        return response()->json($address);
    }

    public function destroy($id, Request $request)
    {
        $address = CustomerAddress::findOrFail($id);
        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
