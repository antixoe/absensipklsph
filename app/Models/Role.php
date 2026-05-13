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
    public const KESISWAAN = 'kesiswaan';         // Student Affairs - Admin equivalent, creates QR codes
    public const KURIKULUM = 'kurikulum';         // Curriculum
    public const WALI_KELAS = 'wali_kelas';       // Homeroom Teacher
    public const GURU = 'guru';                   // Teacher - Scanner holder
    public const MURID = 'murid';                 // Student
    public const KETUA_KELAS = 'ketua_kelas';     // Class Leader
    public const SEKRETARIS_KELAS = 'sekretaris_kelas';  // Class Secretary
    
    // Deprecated - kept for backward compatibility
    public const STUDENT = 'murid';
    public const TEACHER = 'guru';

    /**
     * Get the normalized role aliases mapped to their canonical names.
     */
    public static function roleAliases(): array
    {
        return [
            self::KESISWAAN => ['kesiswaan', 'student_affairs', 'admin'],
            self::KURIKULUM => ['kurikulum', 'curriculum'],
            self::WALI_KELAS => ['wali_kelas', 'homeroom_teacher', 'walikelas'],
            self::GURU => ['guru', 'teacher'],
            self::MURID => ['murid', 'student', 'siswa'],
            self::KETUA_KELAS => ['ketua_kelas', 'class_leader', 'class_captain'],
            self::SEKRETARIS_KELAS => ['sekretaris_kelas', 'class_secretary'],
        ];
    }

    /**
     * Normalize a role name or slug to its canonical internal name.
     */
    public static function normalizeRoleName(string $roleName): string
    {
        $normalized = strtolower(trim($roleName));
        $normalized = preg_replace('/[^a-z0-9]+/', '_', $normalized) ?? $normalized;
        $normalized = trim($normalized, '_');

        foreach (self::roleAliases() as $canonical => $aliases) {
            if (in_array($normalized, $aliases, true)) {
                return $canonical;
            }
        }

        return $normalized;
    }

    /**
     * Get a display label for a role name using Indonesian labels where possible.
     */
    public static function displayName(string $roleName): string
    {
        $raw = strtolower(trim($roleName));

        if ($raw === 'admin') {
            return 'Admin';
        }

        $canonical = self::normalizeRoleName($roleName);

        return match ($canonical) {
            self::KESISWAAN => 'Kesiswaan',
            self::KURIKULUM => 'Kurikulum',
            self::WALI_KELAS => 'Wali Kelas',
            self::GURU => 'Guru',
            self::MURID => 'Murid',
            self::KETUA_KELAS => 'Ketua Kelas',
            self::SEKRETARIS_KELAS => 'Sekretaris Kelas',
            default => ucwords(str_replace('_', ' ', $canonical)),
        };
    }

    public static function systemRoleNames(): array
    {
        return [];
    }

    public function isSystemRole(): bool
    {
        return false;
    }

    /**
     * Resolve a role model by name or alias.
     */
    public static function resolveByName(string $roleName): ?self
    {
        $canonical = self::normalizeRoleName($roleName);

        return self::query()
            ->get()
            ->first(fn (self $role) => self::normalizeRoleName($role->name) === $canonical);
    }

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
