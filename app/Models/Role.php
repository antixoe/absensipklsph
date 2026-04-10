<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Predefined role constants
    public const STUDENT = 'student';
    public const INDUSTRY_SUPERVISOR = 'industry_supervisor';
    public const HEAD_OF_DEPARTMENT = 'head_of_department';
    public const HOMEROOM_TEACHER = 'homeroom_teacher';
    public const SCHOOL_PRINCIPAL = 'school_principal';
    public const ADMIN = 'admin';

    /**
     * Get the users that have this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the features that this role has access to.
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'role_features');
    }

    /**
     * Check if role has a specific feature.
     */
    public function hasFeature(string $featureSlug): bool
    {
        return $this->features()
            ->where('features.slug', $featureSlug)
            ->where('features.is_active', true)
            ->exists();
    }
}
