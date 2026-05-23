<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{

    public function index()
{
    $products = Cache::remember('products_list', 60, function () {
        return Product::all(['id', 'name', 'description', 'price', 'quantity']);
    });

    return response()->json($products);
}


    public function displayOneProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json($product);
    }
}
