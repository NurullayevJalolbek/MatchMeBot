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
        'balance',
        'daily_streak',
        'is_vip',
        'vip_expires_at',
        'is_terms_accepted',
        'terms_accepted_at',
        'email',
        'password',
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
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'onboarding_completed' => 'boolean',
            'balance' => 'decimal:2',
            'daily_streak' => 'integer',
            'is_vip' => 'boolean',
            'vip_expires_at' => 'datetime',
            'boost_expires_at' => 'datetime',
            'is_terms_accepted' => 'boolean',
            'terms_accepted_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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
     * User balance deposits.
     */
    public function deposits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Deposit::class);
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
    public function getPhotoUrlsAttribute(): array
    {
        return $this->photos->map(fn (ModelFile $file) => $file->url)->toArray();
    }
}
