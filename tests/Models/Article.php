<?php

namespace Altek\Accountant\Tests\Models;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model implements Recordable
{
    use \Altek\Accountant\Recordable;
    use SoftDeletes;

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
     * Uppercase Title accessor.
     *
     * @param string $value
     *
     * @return string
     */
    public function getTitleAttribute(string $value): string
    {
        return strtoupper($value);
    }
}
