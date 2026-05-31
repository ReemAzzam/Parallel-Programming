<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{

    /**
     * BEFORE: بدون Caching
     */
    public function indexBefore()
    {
        $start = microtime(true);

        $products = Product::select('id', 'name', 'description', 'price', 'quantity')->get();

        $duration = round((microtime(true) - $start) * 1000, 2);

        return response()->json([
            'version' => 'BEFORE - Without Cache',
            'duration_ms' => $duration,
            'data' => $products
        ]);
    }

    /**
     * AFTER: مع Distributed Caching باستخدام Redis
     */
    public function indexAfter()
    {
        $start = microtime(true);

        $products = Cache::remember('all_products', 600, function () {   // 10 دقائق
            return Product::select('id', 'name', 'description', 'price', 'quantity')->get();
        });

        $duration = round((microtime(true) - $start) * 1000, 2);

        return response()->json([
            'version' => 'AFTER - With Redis Cache',
            'duration_ms' => $duration,
            'data' => $products,
            'note' => 'Data retrieved from cache (Redis)'
        ]);
    }

    // يمكنك عمل نفس الشيء لدالة displayOneProduct
    public function displayOneProductBefore($id)
    {
        $product = Product::find($id, ['id', 'name', 'description', 'price', 'quantity']);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function displayOneProductAfter($id)
    {
        $product = Cache::remember("product_{$id}", 300, function () use ($id) {
            return Product::find($id, ['id', 'name', 'description', 'price', 'quantity']);
        });

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }
}
