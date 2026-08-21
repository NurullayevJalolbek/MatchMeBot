<?php

namespace App\Contracts;

use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface iPaymentService
{
    /**
     * Paginate payments with filtering.
     */
    public function paginatePayments(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Approve a payment and activate corresponding Subscription or Boost.
     */
    public function approvePayment(Payment $payment, int $adminId): Payment;

    /**
     * Reject/Refund a payment with reason.
     */
    public function rejectPayment(Payment $payment, string $reason, int $adminId): Payment;

    /**
     * Delete a payment record.
     */
    public function deletePayment(Payment $payment): bool;
}
