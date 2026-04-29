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
    public const STUDENT = 'siswa';
    public const INDUSTRY_SUPERVISOR = 'pembimbing_pkl';
    public const HEAD_OF_DEPARTMENT = 'kepala_jurusan';
    public const HOMEROOM_TEACHER = 'wali_kelas';
    public const SCHOOL_PRINCIPAL = 'kepala_sekolah';
    public const SCHOOL_SUPERVISOR = 'pembimbing_sekolah';
    public const STUDENT_AFFAIRS = 'kesiswaan';
    public const ADMIN = 'admin';

    /**
     * Get the normalized role aliases mapped to their canonical names.
     */
    public static function roleAliases(): array
    {
        return [
            self::ADMIN => ['admin', 'administrator', 'administrator_sistem'],
            self::STUDENT => ['student', 'siswa', 'murid'],
            self::INDUSTRY_SUPERVISOR => ['industry_supervisor', 'pembimbing_pkl', 'pembimbing', 'pembimbing_perusahaan', 'pembimbing_industri'],
            self::SCHOOL_SUPERVISOR => ['pembimbing_sekolah', 'guru_pembimbing', 'guru_pembimbing_sekolah', 'guru_pembimbing_sekolah'],
            self::HEAD_OF_DEPARTMENT => ['head_of_department', 'kepala_jurusan', 'ketua_jurusan'],
            self::HOMEROOM_TEACHER => ['homeroom_teacher', 'wali_kelas', 'walikelas'],
            self::SCHOOL_PRINCIPAL => ['school_principal', 'kepala_sekolah'],
            self::STUDENT_AFFAIRS => ['kesiswaan', 'student_affairs'],
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
     * Canonical role names that should be treated as system roles.
     */
    public static function systemRoleNames(): array
    {
        return [
            self::ADMIN,
            self::STUDENT,
            self::INDUSTRY_SUPERVISOR,
            self::SCHOOL_SUPERVISOR,
            self::HEAD_OF_DEPARTMENT,
            self::HOMEROOM_TEACHER,
            self::SCHOOL_PRINCIPAL,
            self::STUDENT_AFFAIRS,
        ];
    }

    /**
     * Determine whether this role is a protected system role.
     */
    public function isSystemRole(): bool
    {
        return in_array(self::normalizeRoleName($this->name), self::systemRoleNames(), true);
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
