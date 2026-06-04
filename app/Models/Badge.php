<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'type',
        'requirement',
        'active',
    ];

    protected $casts = [
        'requirement' => 'integer',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Les utilisateurs qui ont ce badge
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot('earned_at', 'context')
                    ->withTimestamps();
    }

    /**
     * Scope pour les badges actifs
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Vérifier si un utilisateur a ce badge
     */
    public function hasUser(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Obtenir la couleur du badge selon son type
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'platinum' => '#e5e4e2',
            'gold' => '#ffd700',
            'silver' => '#c0c0c0',
            'bronze' => '#cd7f32',
            default => '#8b4513',
        };
    }
}
