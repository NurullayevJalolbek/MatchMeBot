<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserBoost;
use App\Models\UserSubscription;
use Illuminate\Console\Command;

class CheckExpiredServicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muddati o\'tgan Premium Obunalar va Boostlarni avtomatik aniqlab expired holatiga o\'tkazish';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        // 1. Muddati tugagan Obunalarni tekshirish
        $expiredSubscriptions = UserSubscription::where('status', 'active')
            ->where('ends_at', '<=', $now)
            ->get();

        $subCount = 0;
        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update([
                'status' => 'expired',
                'is_active' => false,
            ]);

            // Foydalanuvchida boshqa faol obuna bormi tekshirish
            $hasActiveSub = UserSubscription::where('user_id', $subscription->user_id)
                ->where('status', 'active')
                ->where('ends_at', '>', $now)
                ->exists();

            if (!$hasActiveSub) {
                User::where('id', $subscription->user_id)->update([
                    'is_premium' => false,
                ]);
            }

            $subCount++;
        }

        // 2. Muddati tugagan Boostlarni tekshirish
        $expiredBoosts = UserBoost::where('status', 'active')
            ->where('ends_at', '<=', $now)
            ->get();

        $boostCount = 0;
        foreach ($expiredBoosts as $boost) {
            $boost->update([
                'status' => 'expired',
                'is_active' => false,
            ]);
            $boostCount++;
        }

        $this->info("Tekshiruv yakunlandi: {$subCount} ta obuna va {$boostCount} ta boost muddati tugatildi.");

        return Command::SUCCESS;
    }
}
