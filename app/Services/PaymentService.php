<?php

namespace App\Services;

use App\Contracts\iPaymentService;
use App\Enums\Finance\PaymentStatusEnum;
use App\Enums\Subscription\SubscriptionPeriodTypeEnum;
use App\Models\BoostPlan;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserBoost;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService implements iPaymentService
{
    /**
     * Paginate payments with filtering.
     */
    public function paginatePayments(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Payment::query()->with([
            'user',
            'incomeCategory.parent',
            'payable',
            'approver',
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('username', 'ilike', "%{$search}%")
                  ->orWhere('telegram_id', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            if ($filters['type'] === 'subscription') {
                $query->where('payable_type', SubscriptionPlan::class);
            } elseif ($filters['type'] === 'boost') {
                $query->where('payable_type', BoostPlan::class);
            }
        }

        return $query->orderBy('status', 'ASC') // pending first
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Approve a payment and activate corresponding Subscription or Boost.
     */
    public function approvePayment(Payment $payment, int $adminId): Payment
    {
        $payment->update([
            'status' => PaymentStatusEnum::APPROVED,
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);

        $user = $payment->user;
        $payable = $payment->payable;

        if ($payable instanceof SubscriptionPlan) {
            // Davomiylikni hisoblash
            $periodCount = (int) ($payable->period_count ?: 1);
            $periodType = $payable->period_type instanceof SubscriptionPeriodTypeEnum 
                ? $payable->period_type->value 
                : ($payable->period_type ?: 'month');

            $now = Carbon::now();
            $endsAt = match ($periodType) {
                'day' => $now->copy()->addDays($periodCount),
                'week' => $now->copy()->addWeeks($periodCount),
                'month' => $now->copy()->addMonths($periodCount),
                'year' => $now->copy()->addYears($periodCount),
                default => $now->copy()->addMonths(1),
            };

            // Agar avvalgi faol obunasi bo'lsa va muddati hali tugamagan bo'lsa, davomidan qo'shish
            $activeSub = UserSubscription::where('user_id', $payment->user_id)
                ->where('status', 'active')
                ->where('ends_at', '>', now())
                ->latest('ends_at')
                ->first();

            if ($activeSub) {
                $endsAt = match ($periodType) {
                    'day' => $activeSub->ends_at->copy()->addDays($periodCount),
                    'week' => $activeSub->ends_at->copy()->addWeeks($periodCount),
                    'month' => $activeSub->ends_at->copy()->addMonths($periodCount),
                    'year' => $activeSub->ends_at->copy()->addYears($periodCount),
                    default => $activeSub->ends_at->copy()->addMonths(1),
                };
            }

            UserSubscription::create([
                'user_id' => $payment->user_id,
                'subscription_plan_id' => $payable->id,
                'payment_id' => $payment->id,
                'starts_at' => now(),
                'ends_at' => $endsAt,
                'status' => 'active',
                'is_active' => true,
            ]);

            // User profilida premium belgisini yoqish
            if ($user) {
                $user->update([
                    'is_premium' => true,
                ]);

                $this->sendTelegramMessage(
                    $user->telegram_id,
                    "🎉 <b>To'lovingiz muvaffaqiyatli tasdiqlandi!</b>\n\n" .
                    "👑 <b>Tarif:</b> {$payable->title}\n" .
                    "📅 <b>Faol muddat:</b> " . format_datetime($endsAt) . " gacha\n\n" .
                    "<i>MatchMe Premium imkoniyatlaridan to'liq bahramand bo'ling! 🚀</i>"
                );
            }
        } elseif ($payable instanceof BoostPlan) {
            $hours = (int) ($payable->hours ?: 1);
            $endsAt = Carbon::now()->addHours($hours);

            UserBoost::create([
                'user_id' => $payment->user_id,
                'boost_plan_id' => $payable->id,
                'payment_id' => $payment->id,
                'starts_at' => now(),
                'ends_at' => $endsAt,
                'status' => 'active',
                'is_active' => true,
            ]);

            if ($user) {
                $this->sendTelegramMessage(
                    $user->telegram_id,
                    "⚡ <b>Boost xizmatingiz faollashtirildi!</b>\n\n" .
                    "🚀 <b>Reja:</b> {$payable->title}\n" .
                    "⏱ <b>Amal qilish muddati:</b> " . format_datetime($endsAt) . " gacha\n\n" .
                    "<i>Profilingiz barcha qidiruvlarda birinchi o'rinlarda ko'rsatiladi! 🔥</i>"
                );
            }
        }

        return $payment;
    }

    /**
     * Reject/Refund a payment with reason.
     */
    public function rejectPayment(Payment $payment, string $reason, int $adminId): Payment
    {
        $payment->update([
            'status' => PaymentStatusEnum::REJECTED,
            'rejection_reason' => $reason,
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);

        $user = $payment->user;
        if ($user && $user->telegram_id) {
            $this->sendTelegramMessage(
                $user->telegram_id,
                "⚠️ <b>To'lovingiz rad etildi / qaytarildi</b>\n\n" .
                "❌ <b>Sabab:</b> " . ($reason ?: "To'lov cheki tasdiqlanmadi.") . "\n\n" .
                "<i>Iltimos, qaytadan urinib ko'ring yoki qo'llab-quvvatlash xizmati bilan bog'laning.</i>"
            );
        }

        return $payment;
    }

    /**
     * Delete a payment record.
     */
    public function deletePayment(Payment $payment): bool
    {
        return (bool) $payment->delete();
    }

    /**
     * Telegram bot orqali xabar yuborish yordamchisi.
     */
    protected function sendTelegramMessage(?string $telegramId, string $message): void
    {
        if (!$telegramId) {
            return;
        }

        $botToken = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
        if (!$botToken) {
            return;
        }

        try {
            Http::timeout(4)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $telegramId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Telegram notification failed: ' . $e->getMessage());
        }
    }
}
