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
    public const INDUSTRY_SUPERVISOR = 'pembimbing_sekolah';
    public const COMPANY_MENTOR = 'pembimbing_perusahaan';
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
            self::INDUSTRY_SUPERVISOR => ['industry_supervisor', 'pembimbing_sekolah', 'pembimbing_industri'],
            self::COMPANY_MENTOR => ['company_mentor', 'pembimbing_perusahaan', 'pembimbing'],
            self::SCHOOL_SUPERVISOR => ['pembimbing_sekolah', 'guru_pembimbing', 'guru_pembimbing_sekolah'],
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
     * Get a display label for a role name using Indonesian labels where possible.
     */
    public static function displayName(string $roleName): string
    {
        $canonical = self::normalizeRoleName($roleName);

        return match ($canonical) {
            self::ADMIN => 'Admin',
            self::STUDENT => 'Siswa',
            self::INDUSTRY_SUPERVISOR => 'Pembimbing Sekolah',
            self::COMPANY_MENTOR => 'Pembimbing Perusahaan',
            self::HEAD_OF_DEPARTMENT => 'Kepala Jurusan',
            self::HOMEROOM_TEACHER => 'Wali Kelas',
            self::SCHOOL_PRINCIPAL => 'Kepala Sekolah',
            self::SCHOOL_SUPERVISOR => 'Pembimbing Sekolah',
            self::STUDENT_AFFAIRS => 'Kesiswaan',
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
