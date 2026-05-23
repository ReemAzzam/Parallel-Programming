<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessSalesChunkJob;
use App\Models\Order;

class ReportController extends Controller
{
        public function generateDailyReport()
    {
        $orders = Order::all();

        $totalSales = 0;

        foreach ($orders as $order) {
            $totalSales += $order->amount * $order->product->price;
        }

        return response()->json([
            'total_sales' => $totalSales
        ]);
    }

    //4.Batch processing
    public function generateDailyReportWithJob()
    {
        Order::with('product')
            ->chunk(30, function ($orders) {

                ProcessSalesChunkJob::dispatch($orders);

            });

        return response()->json([
            'message' => 'Daily report processing started'
        ]);
    }

}
