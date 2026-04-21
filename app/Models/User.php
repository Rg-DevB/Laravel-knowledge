<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Model;




#[Fillable(['name', 'email', 'password', 'avatar', 'bio', 'github_url', 'twitter_url', 'website_url', 'reputation', 'role', 'username'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // ── Relationships ──────────────────────────────────────────
 
    public function problems(): HasMany
    {
        return $this->hasMany(Problem::class);
    }
 
    public function solutions(): HasMany
    {
        return $this->hasMany(Solution::class);
    }
 
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
 
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
 
    public function favorites(): MorphToMany
    {
        return $this->morphedByMany(Problem::class, 'favoritable', 'favorites');
    }
 
    public function reputationLogs(): HasMany
    {
        return $this->hasMany(ReputationLog::class);
    }
 
    public function follows(): HasMany
    {
        return $this->hasMany(Follow::class);
    }
 
    // ── Helpers ────────────────────────────────────────────────
 
    public function addReputation(int $points, string $reason, Model $reference): void
    {
        $this->increment('reputation', $points);
        $this->reputationLogs()->create([
            'points'            => $points,
            'reason'            => $reason,
            'referenceable_id'  => $reference->id,
            'referenceable_type'=> $reference::class,
        ]);
    }
 
    public function reputationBadge(): string
    {
        return match (true) {
            $this->reputation >= 10000 => 'Legend',
            $this->reputation >= 5000  => 'Expert',
            $this->reputation >= 1000  => 'Contributor',
            $this->reputation >= 100   => 'Member',
            default                    => 'Newcomer',
        };
    }
 
    public function hasVoted(Model $model): ?int
    {
        return $this->votes()
            ->where('votable_id', $model->id)
            ->where('votable_type', $model::class)
            ->value('value');
    }
 
    public function hasFavorited(Model $model): bool
    {
        return \DB::table('favorites')
            ->where('user_id', $this->id)
            ->where('favoritable_id', $model->id)
            ->where('favoritable_type', $model::class)
            ->exists();
    }
}
