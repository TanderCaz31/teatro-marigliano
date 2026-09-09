<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Show extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration_minutes',
        'is_featured',
    ];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean'];
    }

    public function performances(): HasMany
    {
        return $this->hasMany(Performance::class);
    }
}
