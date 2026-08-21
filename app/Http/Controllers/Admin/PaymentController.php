<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\iPaymentService;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected iPaymentService $paymentService
    ) {}

    /**
     * Display a listing of payments.
     */
    public function index(Request $request): View
    {
        $payments = $this->paymentService->paginatePayments(
            $request->only(['status', 'search', 'type']), 
            15
        );

        return view('admin.pages.payments.index', [
            'datas' => $payments,
            'payments' => $payments,
            'currentStatus' => $request->get('status'),
        ]);
    }

    /**
     * Display the specified payment receipt & details.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['user', 'incomeCategory.parent', 'payable', 'approver', 'userSubscription', 'userBoost']);

        return view('admin.pages.payments.show', [
            'payment' => $payment,
        ]);
    }

    /**
     * Approve payment and activate subscription/boost.
     */
    public function approve(Payment $payment)
    {
        $this->paymentService->approvePayment($payment, auth()->id() ?: 1);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'To\'lov muvaffaqiyatli tasdiqlandi va xizmat faollashtirildi!',
            ]);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'To\'lov muvaffaqiyatli tasdiqlandi va xizmat faollashtirildi!');
    }

    /**
     * Reject/Refund payment with reason.
     */
    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ], [
            'reason.required' => 'Rad etish sababini kiritish majburiy',
        ]);

        $this->paymentService->rejectPayment($payment, $request->get('reason'), auth()->id() ?: 1);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'To\'lov rad etildi va foydalanuvchiga sababi yuborildi!',
            ]);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'To\'lov rad etildi va foydalanuvchiga sababi yuborildi!');
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Request $request, Payment $payment)
    {
        $this->paymentService->deletePayment($payment);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'To\'lov yozuvi muvaffaqiyatli o\'chirildi!',
            ]);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'To\'lov yozuvi muvaffaqiyatli o\'chirildi!');
    }
}
