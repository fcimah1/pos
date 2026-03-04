<?php

namespace App\Repository;

use App\Models\Product;
use App\Models\ProductVariation;
use App\DTOs\ProductDTO;
use App\Repository\Interface\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAll(int $branchId)
    {
        return Product::with(['category', 'variations'])
            ->where('branch_id', $branchId)
            ->get();
    }

    public function findById(int $id)
    {
        return Product::with('variations')->findOrFail($id);
    }

    public function create(ProductDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            $product = Product::create([
                'branch_id'   => $dto->branch_id,
                'category_id' => $dto->category_id,
                'name'        => $dto->name,
                'barcode'     => $dto->barcode,
                'price'       => $dto->price,
                'cost_price'  => $dto->cost_price,
                'icon'        => $dto->icon,
                'is_active'   => $dto->is_active,
            ]);

            foreach ($dto->variations as $varDTO) {
                $product->variations()->create([
                    'size_name' => $varDTO->size_name,
                    'price' => $varDTO->price,
                    'cost_price' => $varDTO->cost_price,
                    'barcode' => $varDTO->barcode,
                ]);
            }

            // في حال لم تُرسل أحجام، ننشئ حجمًا افتراضيًا "عادي"
            if (count($dto->variations) === 0) {
                $product->variations()->create([
                    'size_name'  => 'عادي',
                    'price'      => $dto->price,
                    'cost_price' => $dto->cost_price,
                    'barcode'    => $product->barcode,
                ]);
            }

            return $product->load('variations');
        });
    }

    public function update(int $id, ProductDTO $dto)
    {
        return DB::transaction(function () use ($id, $dto) {
            $product = $this->findById($id);
            $product->update([
                'category_id' => $dto->category_id,
                'name'        => $dto->name,
                'barcode'     => $dto->barcode,
                'price'       => $dto->price,
                'cost_price'  => $dto->cost_price,
                'icon'        => $dto->icon,
                'is_active'   => $dto->is_active,
            ]);

            // Sync variations: Delete missing, update existing, create new
            $keptVariationIds = [];
            foreach ($dto->variations as $varDTO) {
                if ($varDTO->id) {
                    $variation = $product->variations()->find($varDTO->id);
                    if ($variation) {
                        $variation->update([
                            'size_name' => $varDTO->size_name,
                            'price' => $varDTO->price,
                            'cost_price' => $varDTO->cost_price,
                            'barcode' => $varDTO->barcode,
                        ]);
                        $keptVariationIds[] = $variation->id;
                    }
                } else {
                    $newVariation = $product->variations()->create([
                        'size_name' => $varDTO->size_name,
                        'price' => $varDTO->price,
                        'cost_price' => $varDTO->cost_price,
                        'barcode' => $varDTO->barcode,
                    ]);
                    $keptVariationIds[] = $newVariation->id;
                }
            }

            if (count($keptVariationIds) > 0) {
                $product->variations()->whereNotIn('id', $keptVariationIds)->delete();
            } else {
                // لا توجد أحجام مرسلة: حافظ/أنشئ الحجم الافتراضي "عادي"
                $existing = $product->variations()->first();
                if ($existing) {
                    $existing->update([
                        'size_name'  => 'عادي',
                        'price'      => $dto->price,
                        'cost_price' => $dto->cost_price,
                        'barcode'    => $product->barcode,
                    ]);
                    $keptVariationIds[] = $existing->id;
                    $product->variations()->whereNotIn('id', $keptVariationIds)->delete();
                } else {
                    $product->variations()->create([
                        'size_name'  => 'عادي',
                        'price'      => $dto->price,
                        'cost_price' => $dto->cost_price,
                        'barcode'    => $product->barcode,
                    ]);
                }
            }

            return $product->load('variations');
        });
    }

    public function delete(int $id, int $branchId)
    {
        $product = Product::where('branch_id', $branchId)->findOrFail($id);
        return $product->delete();
    }
}
