<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $title
 * @property string|null $description
 * @property int|null    $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 'description', 'user_id',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
