<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'status'];

    // Level constants
    const KESISWAAN = 'kesiswaan';
    const WALI_KELAS = 'wali_kelas';
    const KETUA_KELAS = 'ketua_kelas';
    const SEKRETARIS_KELAS = 'sekretaris_kelas';

    /**
     * Get all available levels
     */
    public static function getAllLevels()
    {
        return [
            self::KESISWAAN => 'Kesiswaan',
            self::WALI_KELAS => 'Wali Kelas',
            self::KETUA_KELAS => 'Ketua Kelas',
            self::SEKRETARIS_KELAS => 'Sekretaris Kelas',
        ];
    }

    /**
     * Get display name for level
     */
    public static function displayName($name)
    {
        $levels = self::getAllLevels();
        return $levels[$name] ?? $name;
    }

    /**
     * Get levels for a specific role
     */
    public static function getLevelsForRole($roleName)
    {
        $levelsByRole = [
            Role::GURU => [self::KESISWAAN, self::WALI_KELAS],
            Role::MURID => [self::KETUA_KELAS, self::SEKRETARIS_KELAS],
        ];

        return $levelsByRole[$roleName] ?? [];
    }

    /**
     * Relationship: Get users with this level
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

