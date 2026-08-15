<?php

namespace App\Contracts;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Http\UploadedFile;

interface iWalletService
{
    /**
     * Get user current balance and summary.
     */
    public function getBalance(User $user): array;

    /**
     * Create a new deposit request with payment receipt.
     */
    public function createDeposit(User $user, float $amount, ?UploadedFile $receiptFile): Deposit;

    /**
     * Approve deposit request and credit user balance.
     */
    public function approveDeposit(Deposit $deposit, ?string $adminNote = null): bool;

    /**
     * Reject deposit request with reason.
     */
    public function rejectDeposit(Deposit $deposit, ?string $adminNote = null): bool;
}
