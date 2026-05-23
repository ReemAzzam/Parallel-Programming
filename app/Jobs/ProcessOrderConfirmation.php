<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrderConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $totalPrice;
    public $ordersData;

    public function __construct($userId, $totalPrice, $ordersData = [])
    {
        $this->userId = $userId;
        $this->totalPrice = $totalPrice;
        $this->ordersData = $ordersData;
    }

    public function handle()
{
    $start = microtime(true);

    echo " [JOB] 🚀 بدء المعالجة الخلفية (Async) لليوزر: {$this->userId}\n";
    Log::info(" [JOB] بدء المعالجة في الخلفية", ['user_id' => $this->userId]);

    sleep(5);   // زيادة لتوضيح الفرق

    $duration = round((microtime(true) - $start) * 1000, 2);

    echo " [JOB] ✅ انتهت المعالجة الخلفية لليوزر: {$this->userId} في {$duration} ms\n";

    Log::info(" [JOB] تم الانتهاء من معالجة الطلب بنجاح", [
        'user_id' => $this->userId,
        'total_price' => $this->totalPrice,
        'job_duration_ms' => $duration
    ]);
}
}
