<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitor
{
    /**
     * Handle an incoming request.
     * هذا الـ Middleware هو Aspect الرئيسي لمراقبة الأداء والـ Logging
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $requestId = uniqid('req_');

        // قبل تنفيذ الطلب (Before Advice)
        Log::info("REQUEST START | Method: " . $request->method() . " | URL: " . $request->url(), [
            'request_id' => $requestId,
            'user_id'    => Auth::id(),
            'ip'         => $request->ip(),
        ]);

        echo "[AOP MONITOR] REQUEST STARTED - " . $request->method() . " " . $request->url() .
             " | Request ID: " . $requestId . "\n";

        // تنفيذ الطلب الأساسي
        $response = $next($request);

        // بعد تنفيذ الطلب (After Advice)
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        Log::info("REQUEST END | Duration: " . $duration . "ms | Status: " . $response->status(), [
            'request_id'  => $requestId,
            'user_id'     => Auth::id(),
            'status_code' => $response->status(),
        ]);

        echo "[AOP MONITOR] REQUEST ENDED - Duration: " . $duration . " ms | Status: " .
             $response->status() . "\n";

        return $response;
    }
}
