<?php

namespace App\Http\Controllers;

use App\Services\MpesaService;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Log;

class MpesaCallbackController extends Controller
{
    public function __construct(private MpesaService $mpesaService) {}

    public function handle(Request $request): JsonResponse
    {
        Log::info('M-Pesa callback received', $request->all());

        try {
            $this->mpesaService->handleCallback($request->all());
        } catch (\Throwable $e) {
            Log::error('M-Pesa callback error', [
                'message' => $e->getMessage(),
                'payload' => $request->all(),
            ]);
        }

        // Safaricom expects this exact response — always return 200
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }
}
