<?php

namespace App\Contracts;

use App\Models\User;

interface iLikesService
{
    /**
     * Send a like or VIP gift to a target user.
     */
    public function sendLike(User $fromUser, int $targetUserId, bool $isGift = false, ?string $giftName = null, ?string $giftIcon = null): array;

    /**
     * Get VIP gift senders and normal likes for user.
     */
    public function getLikesData(User $user): array;

    /**
     * Accept a like/gift (match with user).
     */
    public function acceptLike(User $user, int $likeId): array;

    /**
     * Reject/dismiss a like/gift.
     */
    public function rejectLike(User $user, int $likeId): array;
}
