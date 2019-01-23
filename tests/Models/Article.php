<?php

declare(strict_types=1);

namespace Altek\Accountant\Tests\Models;

use Altek\Accountant\Contracts\Recordable;
use Altek\Eventually\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;
    use \Altek\Eventually\Eventually;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * {@inheritdoc}
     */
    protected $table = 'articles';

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'reviewed' => 'bool',
    ];

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'published_at',
        'deleted_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'title',
        'content',
        'published_at',
        'reviewed',
    ];

    /**
     * Associated Users.
     *
     * @return \Altek\Eventually\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('liked')
            ->withTimestamps();
    }

    /**
     * Uppercase Title accessor.
     *
     * @param string $value
     *
     * @return string
     */
    public function getTitleAttribute(string $value): string
    {
        return \mb_strtoupper($value);
    }
}
