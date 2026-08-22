<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'telegram_id',
        'first_name',
        'last_name',
        'username',
        'language_code',
        'name',
        'birth_date',
        'age',
        'gender',
        'looking_for',
        'city',
        'latitude',
        'longitude',
        'bio',
        'onboarding_completed',
        'daily_streak',
        'is_vip',
        'vip_expires_at',
        'is_terms_accepted',
        'terms_accepted_at',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'telegram_id' => 'integer',
            'birth_date' => 'date:Y-m-d',
            'age' => 'integer',
            'gender' => \App\Enums\User\GenderEnum::class,
            'looking_for' => \App\Enums\User\GenderEnum::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'onboarding_completed' => 'boolean',
            'daily_streak' => 'integer',
            'is_vip' => 'boolean',
            'vip_expires_at' => 'datetime',
            'boost_expires_at' => 'datetime',
            'is_terms_accepted' => 'boolean',
            'terms_accepted_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => \App\Enums\Admin\AdminStatusEnum::class,
        ];
    }

    /**
     * Scope a query to only include admins.
     */
    public function scopeAdmins($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('code', \App\Enums\User\RoleEnum::ADMIN->value);
        });
    }

    /**
     * All model files (polymorphic).
     */
    public function modelFiles(): MorphMany
    {
        return $this->morphMany(ModelFile::class, 'model')->orderBy('order');
    }

    /**
     * User profile photos relation.
     */
    public function photos(): MorphMany
    {
        return $this->morphMany(ModelFile::class, 'model')
            ->where('file_type', 'photo')
            ->orderBy('order');
    }

    /**
     * User discovery search filter preferences.
     */
    public function filter(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserFilter::class);
    }

    /**
     * Primary / Main photo.
     */
    public function primaryPhoto(): MorphOne
    {
        return $this->morphOne(ModelFile::class, 'model')
            ->where('file_type', 'photo')
            ->where('is_main', true);
    }

    /**
     * Likes/Gifts received by this user.
     */
    public function receivedLikes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserLike::class, 'to_user_id');
    }

    /**
     * Likes/Gifts sent by this user.
     */
    public function sentLikes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserLike::class, 'from_user_id');
    }

    /**
     * Get user's display name.
     */
    public function getFullNameAttribute(): string
    {
        if (!empty($this->name)) {
            return $this->name;
        }

        $fullName = trim("{$this->first_name} {$this->last_name}");
        return !empty($fullName) ? $fullName : ($this->username ?? 'Foydalanuvchi');
    }

    /**
     * Get primary photo URL string attribute.
     */
    public function getPrimaryPhotoUrlAttribute(): ?string
    {
        $mainPhoto = $this->photos()->where('is_main', true)->first()
            ?? $this->photos()->first();

        return $mainPhoto?->url;
    }

    /**
     * Get array of photo URLs.
     *
     * @return array<string>
     */
    /**
     * Get array of photo URLs.
     *
     * @return array<string>
     */
    public function getPhotoUrlsAttribute(): array
    {
        return $this->photos->map(fn (ModelFile $file) => $file->url)->toArray();
    }

    /**
     * User roles relationship.
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }

    /**
     * Check if user has specific role code.
     */
    public function hasRole(string $roleCode): bool
    {
        return $this->roles->contains('code', $roleCode);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(\App\Enums\User\RoleEnum::ADMIN->value);
    }

    /**
     * Assign role to user.
     */
    public function assignRole(string $roleCode): void
    {
        $role = Role::where('code', $roleCode)->first();
        if ($role && !$this->hasRole($roleCode)) {
            $this->roles()->attach($role->id);
        }
    }
}

