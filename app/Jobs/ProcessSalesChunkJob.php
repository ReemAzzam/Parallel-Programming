<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessSalesChunkJob implements ShouldQueue
{
    use Queueable;

    protected $orders;

    /**
     * Create a new job instance.
     */
    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $totalSales = 0;

        foreach ($this->orders as $order) {

            if ($order->product) {

                $totalSales +=
                    $order->amount *
                    $order->product->price;
            }
        }

        \Log::info('Chunk Total Sales: ' . $totalSales);
    }
}