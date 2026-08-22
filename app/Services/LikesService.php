<?php

namespace App\Services;

use App\Contracts\iLikesService;
use App\Enums\Like\LikeStatusEnum;
use App\Models\User;
use App\Models\UserLike;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LikesService implements iLikesService
{
    /**
     * Send a like or VIP gift to a target user and notify via Telegram.
     */
    public function sendLike(User $fromUser, int $targetUserId, bool $isGift = false, ?string $giftName = null, ?string $giftIcon = null): array
    {
        if ($fromUser->id === $targetUserId) {
            return [
                'success' => false,
                'message' => 'O\'zingizga layk bosa olmaysiz',
            ];
        }

        $targetUser = User::find($targetUserId);
        if (!$targetUser) {
            return [
                'success' => false,
                'message' => 'Foydalanuvchi topilmadi',
            ];
        }

        // Check if mutual like (they already liked us)
        $existingTargetLike = UserLike::where('from_user_id', $targetUserId)
            ->where('to_user_id', $fromUser->id)
            ->first();

        $isMatch = false;

        $userLike = UserLike::updateOrCreate(
            [
                'from_user_id' => $fromUser->id,
                'to_user_id' => $targetUserId,
            ],
            [
                'is_gift' => $isGift,
                'gift_name' => $giftName,
                'gift_icon' => $giftIcon,
                'status' => $existingTargetLike ? LikeStatusEnum::ACCEPTED->value : LikeStatusEnum::PENDING->value,
            ]
        );

        $fromName = htmlspecialchars($fromUser->name ?? $fromUser->first_name ?? 'Foydalanuvchi');
        $targetName = htmlspecialchars($targetUser->name ?? $targetUser->first_name ?? 'Foydalanuvchi');

        if ($existingTargetLike) {
            $isMatch = true;
            $existingTargetLike->update(['status' => LikeStatusEnum::ACCEPTED->value]);

            // Notify target user about MATCH
            $this->sendTelegramNotification(
                $targetUser->telegram_id,
                "🎉 <b>Yangi Moslik (Match)!</b>\n\nSiz va <b>{$fromName}</b> bir-biringizni yoqtirdingiz! Suhbatni boshlash uchun Mini-Appni oching. 💬",
                '/likes'
            );

            // Notify current user about MATCH
            $this->sendTelegramNotification(
                $fromUser->telegram_id,
                "🎉 <b>Yangi Moslik (Match)!</b>\n\nSiz va <b>{$targetName}</b> bir-biringizni yoqtirdingiz! Suhbatni boshlash uchun Mini-Appni oching. 💬",
                '/likes'
            );
        } else {
            // Notify target user about new LIKE
            $senderTitle = $isGift ? "🎁 <b>Yangi VIP Sovg'a va Layk!</b>" : "❤️ <b>Sizga yangi layk keldi!</b>";
            $giftDesc = $isGift && $giftName ? "\nSizga <b>{$giftIcon} {$giftName}</b> sovg'asi yuborildi!" : "";
            
            $this->sendTelegramNotification(
                $targetUser->telegram_id,
                "{$senderTitle}\n\nKimdir sizning profilingizni yoqtirdi!{$giftDesc}\nAnketani ko'rish uchun quyidagi tugmani bosing 👇",
                '/likes'
            );
        }

        return [
            'success' => true,
            'message' => $isMatch ? 'Tabriklaymiz! Sizda yangi moslik bor!' : 'Layk yuborildi',
            'is_match' => $isMatch,
            'like_id' => $userLike->id,
        ];
    }

    /**
     * Get VIP gift senders and normal likes for user.
     */
    public function getLikesData(User $user): array
    {
        $allLikes = UserLike::with(['fromUser.photos'])
            ->where('to_user_id', $user->id)
            ->where('status', LikeStatusEnum::PENDING->value)
            ->orderByDesc('created_at')
            ->get();

        // VIP Gift senders
        $vipGifts = $allLikes->where('is_gift', true)->map(function (UserLike $like) {
            $sender = $like->fromUser;
            return [
                'like_id' => $like->id,
                'user_id' => $sender->id,
                'name' => $sender->name ?? $sender->first_name ?? 'Foydalanuvchi',
                'age' => $sender->age ?? null,
                'city' => $sender->city ?? 'Toshkent',
                'photo' => $sender->primary_photo_url ?? asset('assets/images/no-avatar.png'),
                'gift_name' => $like->gift_name ?? 'Sovg\'a',
                'gift_icon' => $like->gift_icon ?? '🎁',
                'badge' => "🎁 {$like->gift_icon} {$like->gift_name}",
                'subtext' => 'VIP Sovg\'a',
            ];
        })->values()->toArray();

        // Regular Likes
        $regularLikes = $allLikes->where('is_gift', false)->map(function (UserLike $like) {
            $sender = $like->fromUser;
            return [
                'like_id' => $like->id,
                'user_id' => $sender->id,
                'name' => $sender->name ?? $sender->first_name ?? 'Foydalanuvchi',
                'age' => $sender->age ?? null,
                'city' => $sender->city ?? 'Toshkent',
                'photo' => $sender->primary_photo_url ?? asset('assets/images/no-avatar.png'),
                'subtext' => ucfirst(str_replace('_', ' ', $sender->city ?? 'Toshkent')),
            ];
        })->values()->toArray();

        return [
            'vip_gifts' => $vipGifts,
            'vip_gifts_count' => count($vipGifts),
            'regular_likes' => $regularLikes,
            'regular_likes_count' => count($regularLikes),
            'total_likes_count' => count($allLikes),
            'daily_streak' => $user->daily_streak ?: 1,
        ];
    }

    /**
     * Accept a like/gift (match with user).
     */
    public function acceptLike(User $user, int $likeId): array
    {
        $like = UserLike::with('fromUser')
            ->where('to_user_id', $user->id)
            ->where('id', $likeId)
            ->first();

        if (!$like) {
            return [
                'success' => false,
                'message' => 'Layk topilmadi!',
            ];
        }

        $like->update(['status' => LikeStatusEnum::ACCEPTED->value]);

        $senderName = $like->fromUser->name ?? $like->fromUser->first_name ?? 'Foydalanuvchi';
        $currentUserName = $user->name ?? $user->first_name ?? 'Foydalanuvchi';

        // Notify the original sender that their like was accepted!
        $this->sendTelegramNotification(
            $like->fromUser->telegram_id,
            "🎉 <b>Tabriklaymiz! Sizda yangi moslik bor!</b>\n\n<b>{$currentUserName}</b> sizning laykingizni qabul qildi. Endi bir-biringiz bilan muloqot qilishingiz mumkin! 💬",
            '/likes'
        );

        return [
            'success' => true,
            'message' => "🎉 {$senderName} bilan o'zaro moslik paydo bo'ldi!",
            'matched_user_id' => $like->from_user_id,
            'matched_user_name' => $senderName,
        ];
    }

    /**
     * Reject/dismiss a like/gift.
     */
    public function rejectLike(User $user, int $likeId): array
    {
        $like = UserLike::where('to_user_id', $user->id)
            ->where('id', $likeId)
            ->first();

        if (!$like) {
            return [
                'success' => false,
                'message' => 'Layk topilmadi!',
            ];
        }

        $like->update(['status' => LikeStatusEnum::REJECTED->value]);

        return [
            'success' => true,
            'message' => 'O\'chirildi',
        ];
    }

    /**
     * Send Telegram notification with Mini-App WebApp button.
     */
    protected function sendTelegramNotification(?int $telegramId, string $message, string $page = '/likes'): void
    {
        if (!$telegramId) {
            return;
        }

        $botToken = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN') ?: env('BOT_TOKEN');
        if (!$botToken) {
            return;
        }

        $appUrl = config('services.telegram.webapp_url') ?: env('TELEGRAM_WEBAPP_URL') ?: env('APP_URL');
        $webAppUrl = rtrim($appUrl, '/') . $page;

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => '❤️ Layklarni ko\'rish',
                        'web_app' => ['url' => $webAppUrl]
                    ]
                ]
            ]
        ];

        try {
            Http::timeout(4)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $telegramId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($keyboard),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Telegram like notification failed: ' . $e->getMessage());
        }
    }
}
