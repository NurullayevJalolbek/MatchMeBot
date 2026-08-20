<?php

namespace Database\Seeders;

use App\Enums\Finance\ExpenseStatusEnum;
use App\Enums\Finance\PaymentMethodEnum;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();

        $tgAds = ExpenseCategory::where('name', 'Telegram Reklama')->first();
        $instaAds = ExpenseCategory::where('name', 'Instagram & Meta Ads')->first();
        $vps = ExpenseCategory::where('name', 'Server & Cloud (VPS)')->first();
        $salary = ExpenseCategory::where('name', 'Dasturchilar Maoshi')->first();

        Expense::query()->delete();

        $expenses = [
            [
                'expense_category_id' => $tgAds?->id,
                'user_id' => $admin?->id,
                'title' => 'Top Telegram kanallarda e\'lonlar joylash',
                'amount' => 1500000,
                'payment_method' => PaymentMethodEnum::CARD->value,
                'spent_at' => now()->subDays(2)->setHour(14)->setMinute(30),
                'status' => ExpenseStatusEnum::APPROVED->value,
                'description' => '5 ta ommabop yoshlar kanallarida 24 soatlik post',
            ],
            [
                'expense_category_id' => $instaAds?->id,
                'user_id' => $admin?->id,
                'title' => 'Instagram Stories Target Reklamasi',
                'amount' => 2200000,
                'payment_method' => PaymentMethodEnum::CARD->value,
                'spent_at' => now()->subDays(1)->setHour(11)->setMinute(15),
                'status' => ExpenseStatusEnum::APPROVED->value,
                'description' => 'Toshkent va Samarqand hududlari bo\'yicha 18-30 yosh maqsadli auditoriya',
            ],
            [
                'expense_category_id' => $vps?->id,
                'user_id' => $admin?->id,
                'title' => 'Hetzner Cloud Server oylik to\'lovi',
                'amount' => 450000,
                'payment_method' => PaymentMethodEnum::BANK->value,
                'spent_at' => now()->subHours(5)->setMinute(0),
                'status' => ExpenseStatusEnum::APPROVED->value,
                'description' => 'PostgreSQL va Redis serverlari uchun oylik to\'lov',
            ],
            [
                'expense_category_id' => $salary?->id,
                'user_id' => $admin?->id,
                'title' => 'Dasturchilar jamoasining oylik avansi',
                'amount' => 8000000,
                'payment_method' => PaymentMethodEnum::BANK->value,
                'spent_at' => now()->subDays(3)->setHour(18)->setMinute(0),
                'status' => ExpenseStatusEnum::PENDING->value,
                'description' => 'Avans to\'lovi bo\'yicha hisob-kitoblar',
            ],
        ];

        foreach ($expenses as $item) {
            if (!empty($item['expense_category_id'])) {
                Expense::create($item);
            }
        }
    }
}
