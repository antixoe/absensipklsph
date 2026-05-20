<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RombonganBelajar extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    /**
     * Students assigned to this learning group.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Users assigned to this learning group.
     *
     * In this application the same `rombel_id` is reused for student accounts
     * and homeroom teacher accounts, so we keep the relation broad.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
