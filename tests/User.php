<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property bool        $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name', 'email', 'active',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Scope a query to only include active users.
     *
     * @param Builder $query
     *
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('active', 1);
    }
}
