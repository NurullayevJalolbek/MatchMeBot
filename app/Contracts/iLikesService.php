<?php

namespace App\Contracts;

use App\Models\User;

interface iLikesService
{
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
