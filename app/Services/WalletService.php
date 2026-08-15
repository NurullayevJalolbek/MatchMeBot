<?php

namespace App\Services;

use App\Contracts\iWalletService;
use App\Enums\Deposit\DepositStatusEnum;
use App\Models\Deposit;
use App\Models\ModelFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class WalletService implements iWalletService
{
    public const PAYMENT_CARD_NUMBER = '5614 6819 1495 1557';
    public const PAYMENT_RECIPIENT_NAME = 'MatchMe Official (JALOLBEK N.)';

    /**
     * Get user current balance and summary.
     */
    public function getBalance(User $user): array
    {
        return [
            'balance' => (float) ($user->balance ?? 0),
            'formatted_balance' => number_format((float) ($user->balance ?? 0), 0, '.', ' ') . ' UZS',
            'is_vip' => (bool) $user->is_vip,
            'vip_expires_at' => $user->vip_expires_at,
            'card_number' => self::PAYMENT_CARD_NUMBER,
            'recipient_name' => self::PAYMENT_RECIPIENT_NAME,
        ];
    }

    /**
     * Create a new deposit request with payment receipt.
     */
    public function createDeposit(User $user, float $amount, ?UploadedFile $receiptFile): Deposit
    {
        return DB::transaction(function () use ($user, $amount, $receiptFile) {
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'status' => DepositStatusEnum::PENDING,
            ]);

            if ($receiptFile && $receiptFile->isValid()) {
                $filename = 'receipt_' . $deposit->id . '_' . time() . '.' . $receiptFile->getClientOriginalExtension();
                $path = $receiptFile->storeAs('receipts', $filename, 'public');

                $deposit->update(['receipt_path' => '/storage/' . $path]);

                // Polymorphic storage
                ModelFile::create([
                    'model_type' => Deposit::class,
                    'model_id' => $deposit->id,
                    'file_path' => $path,
                    'file_type' => 'receipt',
                    'mime_type' => $receiptFile->getMimeType(),
                    'file_size' => $receiptFile->getSize(),
                    'order' => 1,
                    'is_main' => true,
                ]);
            }

            return $deposit;
        });
    }

    /**
     * Approve deposit request and credit user balance.
     */
    public function approveDeposit(Deposit $deposit, ?string $adminNote = null): bool
    {
        if ($deposit->status !== DepositStatusEnum::PENDING) {
            return false;
        }

        return DB::transaction(function () use ($deposit, $adminNote) {
            $deposit->update([
                'status' => DepositStatusEnum::APPROVED,
                'admin_note' => $adminNote,
                'approved_at' => now(),
            ]);

            $deposit->user->increment('balance', $deposit->amount);

            return true;
        });
    }

    /**
     * Reject deposit request with reason.
     */
    public function rejectDeposit(Deposit $deposit, ?string $adminNote = null): bool
    {
        if ($deposit->status !== DepositStatusEnum::PENDING) {
            return false;
        }

        $deposit->update([
            'status' => DepositStatusEnum::REJECTED,
            'admin_note' => $adminNote,
        ]);

        return true;
    }
}
