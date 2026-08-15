<?php

namespace App\Http\Controllers\Api;

use App\Contracts\iWalletService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function __construct(
        protected iWalletService $walletService
    ) {}

    /**
     * Get user balance and card info.
     */
    public function getBalance(Request $request): JsonResponse
    {
        $userId = $request->input('user_id');
        $user = User::find($userId) ?? User::first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $data = $this->walletService->getBalance($user);

        return response()->json([
            'status' => true,
            'message' => __('message.Data retrieved successfully'),
            'data' => $data,
        ]);
    }

    /**
     * Submit deposit payment with receipt.
     */
    public function submitDeposit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric|min:1000|max:100000000',
            'receipt' => 'required|file|mimes:jpeg,png,jpg,pdf,webp|max:10240', // 10MB majburiy
        ], [
            'receipt.required' => 'To\'lov cheki (kvitansiya) rasmini yuklash majburiy!',
            'amount.min' => 'Minimal to\'ldirish summasi 1 000 UZS!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = $request->input('user_id');
        $user = User::find($userId) ?? User::first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => __('message.Not found')], 404);
        }

        $amount = (float) $request->input('amount');
        $receiptFile = $request->file('receipt');

        $deposit = $this->walletService->createDeposit($user, $amount, $receiptFile);

        return response()->json([
            'status' => true,
            'message' => __('message.Successfully created'),
            'data' => [
                'deposit_id' => $deposit->id,
                'amount' => $deposit->amount,
                'status' => $deposit->status,
                'receipt_path' => $deposit->receipt_path,
            ],
        ]);
    }
}
