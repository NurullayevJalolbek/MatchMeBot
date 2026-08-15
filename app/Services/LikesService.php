<?php

namespace App\Services;

use App\Contracts\iLikesService;
use App\Enums\Like\LikeStatusEnum;
use App\Models\User;
use App\Models\UserLike;

class LikesService implements iLikesService
{
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
                'age' => $sender->age ?? 22,
                'city' => $sender->city ?? 'Toshkent',
                'photo' => $sender->primary_photo_url ?? 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=600&auto=format&fit=crop&q=80',
                'gift_name' => $like->gift_name ?? 'Sovg\'a',
                'gift_icon' => $like->gift_icon ?? '🎁',
                'badge' => "🎁 {$like->gift_icon} {$like->gift_name}",
                'subtext' => 'Top-1 Moslik',
            ];
        })->values()->toArray();

        // Regular Likes
        $regularLikes = $allLikes->where('is_gift', false)->map(function (UserLike $like) {
            $sender = $like->fromUser;
            return [
                'like_id' => $like->id,
                'user_id' => $sender->id,
                'name' => $sender->name ?? $sender->first_name ?? 'Foydalanuvchi',
                'age' => $sender->age ?? 21,
                'city' => $sender->city ?? 'Toshkent',
                'photo' => $sender->primary_photo_url ?? 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=600&auto=format&fit=crop&q=80',
                'subtext' => ucfirst(str_replace('_', ' ', $sender->city ?? 'Toshkent')),
            ];
        })->values()->toArray();

        return [
            'vip_gifts' => $vipGifts,
            'vip_gifts_count' => count($vipGifts),
            'regular_likes' => $regularLikes,
            'regular_likes_count' => count($regularLikes),
            'total_likes_count' => count($allLikes),
            'user_balance' => (float) ($user->balance ?? 0),
            'formatted_balance' => number_format((float) ($user->balance ?? 0), 0, '.', ' ') . ' UZS',
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
}
