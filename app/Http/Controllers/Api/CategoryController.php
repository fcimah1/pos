<?php

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('branch_id', $request->user()?->branch_id ?? 1)->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
        ]);

        $category = Category::create([
            'branch_id' => $request->user()?->branch_id ?? 1,
            'name' => $validated['name'],
            'image' => $validated['image'] ?? null,
        ]);

        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::where('id', $id)->where('branch_id', $request->user()?->branch_id ?? 1)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::where('id', $id)->where('branch_id', $request->user()?->branch_id ?? 1)->firstOrFail();
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
