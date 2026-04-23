<?php
// ============================================================
// FILE: app/Models/User.php
// ============================================================
declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\PreferredPosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @OA\Schema(schema="User", type="object")
 */
class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'role', 'dsr_score', 'preferred_position',
        'balance', 'city_id', 'bio',
        'google_id', 'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'role'               => UserRole::class,
        //'preferred_position' => PreferredPosition::class,
        'balance'            => 'decimal:2',
        'dsr_score'          => 'decimal:2',
        'email_verified_at'  => 'datetime',
        'password'           => 'hashed',
    ];

    // ── Relationships ──────────────────────────────────────

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function fields(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Field::class, 'owner_id');
    }

    public function bookings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likedPosts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes');
    }

    public function matchesPlayed(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MatchGame::class, 'match_players',"user_id","match_id");
    }

    public function sentMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function teamRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeamRequest::class, 'player_id');
    }

    // ── Spatie Media Library ───────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    // ── Helpers ───────────────────────────────────────────

    public function isPlayer(): bool
    {
        return $this->role === UserRole::Player;
    }

    public function isOwner(): bool
    {
        return $this->role === UserRole::Owner;
    }

    public function hasSufficientBalance(int|float $amount): bool
    {
        return $this->balance >= $amount;
    }
}
