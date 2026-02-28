<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController
{
    public function index(Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;

        $products = Product::where('branch_id', $branchId)
            ->where('is_active', true)
            ->with('category')
            ->when($request->category_id, function ($query) {
                return $query->where('category_id', $query->request('category_id'));
            })
            ->when($request->search, function ($query) {
                return $query->where('name', 'like', '%' . $query->request('search') . '%')
                    ->orWhere('barcode', $query->request('search'));
            })
            ->paginate(50);

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json($product->load('category'));
    }

    public function byBarcode($barcode, Request $request)
    {
        $branchId = $request->user()?->branch_id ?? 1;

        $product = Product::where('branch_id', $branchId)
            ->where('barcode', $barcode)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json($product);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $product = Product::create([
            'branch_id' => $request->user()?->branch_id ?? 1,
            ...$validated,
            'is_active' => true,
        ]);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('id', $id)->where('branch_id', $request->user()?->branch_id ?? 1)->firstOrFail();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode,' . $id,
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy(Request $request, $id)
    {
        $product = Product::where('id', $id)->where('branch_id', $request->user()?->branch_id ?? 1)->firstOrFail();
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
