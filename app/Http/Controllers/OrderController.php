<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
//use App\Jobs\ProcessOrderConfirmation;

class OrderController extends Controller
{
    // Add to cart
    public function addToOrder(StoreOrderRequest $request)
    {
        $user_id = Auth::id();

        DB::beginTransaction();

        try {
            $product = Product::where('id', $request->product_id)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                return response()->json(['message' => 'product not found'], 404);
            }

            if ($product->quantity < $request->amount) {
                DB::rollBack();
                return response()->json(['message' => 'The quantity is not enough'], 400);
            }

            $order = Order::create([
                'user_id' => $user_id,
                'product_id' => $request->product_id,
                'color' => $request->color,
                'amount' => $request->amount,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Added to cart successfully',
                'order' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // View cart
    public function getUserOrders()
    {
        return response()->json(
            Order::where('user_id', Auth::id())
                ->with('product:id,name,description,price')
                ->get()
        );
    }

    // Update cart
    public function updateOrder(StoreOrderRequest $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->update([
            'color' => $request->color,
            'amount' => $request->amount
        ]);

        return response()->json([
            'message' => 'Updated successfully',
            'order' => $order
        ]);
    }

    // Remove from cart
    public function cancelOrder($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json([
            'message' => 'Removed from cart'
        ]);
    }

// 1. Race Condition:Pessimistic locking:LockForUpdate()+Transaction
// 2. Throttle Middleware+Caching+Query Optimization

// Checkout - BEFORE (Synchronous)
    public function confirmOrderSync(Request $request)
    {
        $startTime = microtime(true);

    $request->validate(['address' => 'required']);

    $user_id = Auth::id();

    DB::beginTransaction();

    try {
        $orders = Order::where('user_id', $user_id)->with('product')->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found'], 400);
        }

        $products = Product::whereIn('id', $orders->pluck('product_id'))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $totalPrice = 0;

        foreach ($orders as $order) {
            $product = $products[$order->product_id] ?? null;

            if (!$product) {
                throw new \Exception('Product not found');
            }

            if ($product->quantity < $order->amount) {
                DB::rollBack();
                return response()->json(['message' => 'Not enough quantity'], 400);
            }

            $product->quantity -= $order->amount;
            $product->save();

            $totalPrice += $order->amount * $product->price;
        }

        Order::where('user_id', $user_id)->delete();

        DB::commit();

        // ==================== AOP + Console Output ====================
        echo "start processing confirmation for user " . $user_id . "\n";

        sleep(3);   // محاكاة العمل الثقيل (إيميل + فاتورة ...)

        echo "done processing confirmation for user " . $user_id . "\n";

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        return response()->json([
            'message' => 'Order confirmed successfully',
            'Total Price' => $totalPrice,
            'version' => 'BEFORE - Synchronous',
            'duration_ms' => $duration
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error in BEFORE confirmOrder", ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Error while confirming order'], 500);
    }
    }

    //3. Checkout - AFTER (Asynchronous)
public function confirmOrderAsync(Request $request)
{
   $startTime = microtime(true);

    $request->validate(['address' => 'required']);

    $user_id = Auth::id();

    DB::beginTransaction();

    try {
        $orders = Order::where('user_id', $user_id)->with('product')->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found'], 400);
        }

        $products = Product::whereIn('id', $orders->pluck('product_id'))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $totalPrice = 0;

        foreach ($orders as $order) {
            $product = $products[$order->product_id] ?? null;
            if (!$product) throw new \Exception('Product not found');

            if ($product->quantity < $order->amount) {
                DB::rollBack();
                return response()->json(['message' => 'Not enough quantity'], 400);
            }

            $product->quantity -= $order->amount;
            $product->save();

            $totalPrice += $order->amount * $product->price;
        }

        Order::where('user_id', $user_id)->delete();

        DB::commit();

        // ====================== ASYNCHRONOUS ======================
        Log::info(" [AFTER] تم حفظ الطلب بنجاح → Job dispatched to Queue", ['user_id' => $user_id]);
        echo " [AFTER] تم حفظ الطلب بنجاح - Job dispatched to background queue for user: {$user_id}\n";

        // إرسال الـ Job
        \App\Jobs\ProcessOrderConfirmation::dispatch($user_id, $totalPrice, $orders->toArray())
            ->onQueue('orders')
            ->delay(now()->addSeconds(2));

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        return response()->json([
            'message' => 'Order confirmed successfully',
            'Total Price' => $totalPrice,
            'version' => 'AFTER - Asynchronous',
            'duration_ms' => $duration,
            'note' => 'All heavy work moved to background queue'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error in AFTER confirmOrder", ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Error', 'error' => $e->getMessage()], 500);
    }
}


}
