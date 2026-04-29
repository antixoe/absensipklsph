<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'status',
        'role_id',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role that the user belongs to.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the student profile associated with this user.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the instructor profile associated with this user.
     */
    public function instructor(): HasOne
    {
        return $this->hasOne(Instructor::class);
    }

    /**
     * Keep student accounts usable even if the profile row was not created elsewhere.
     */
    protected static function booted(): void
    {
        static::saved(function (User $user) {
            $user->ensureStudentProfile();
        });
    }

    /**
     * Ensure a student profile exists for this user.
     */
    public function ensureStudentProfile(): ?Student
    {
        if (!$this->hasRole(Role::STUDENT)) {
            return null;
        }

        return $this->student()->firstOrCreate(
            ['user_id' => $this->id],
            [
                'internship_program_id' => null,
                'nim' => 'AUTO-' . $this->id,
                'school' => null,
                'major' => null,
                'phone' => $this->phone,
                'company_placement' => null,
                'start_date' => null,
                'end_date' => null,
                'status' => 'active',
            ]
        );
    }

    /**
     * Get the activity logs for this user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Check if user has a specific feature.
     */
    public function hasFeature(string $featureSlug): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hasFeature($featureSlug);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role
            && $this->normalizeRoleSlug($this->role->name) === $this->normalizeRoleSlug($roleSlug);
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        if (!$this->role) {
            return false;
        }

        $currentRole = $this->normalizeRoleSlug($this->role->name);
        $allowedRoles = array_map(fn (string $roleSlug) => $this->normalizeRoleSlug($roleSlug), $roleSlugs);

        return in_array($currentRole, $allowedRoles, true);
    }

    /**
     * Normalize role names so access checks are resilient to case and whitespace differences.
     */
    private function normalizeRoleSlug(string $roleSlug): string
    {
        return Role::normalizeRoleName($roleSlug);
    }
}
