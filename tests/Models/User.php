<?php

declare(strict_types=1);

namespace Altek\Accountant\Tests\Models;

use Altek\Accountant\Contracts\Identifiable;
use Altek\Accountant\Contracts\Recordable;
use Altek\Eventually\Relations\BelongsToMany;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Recordable, Identifiable, Authenticatable
{
    use \Altek\Accountant\Recordable;
    use \Altek\Eventually\Eventually;
    use \Illuminate\Auth\Authenticatable;

    /**
     * {@inheritdoc}
     */
    protected $table = 'users';

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'is_admin' => 'bool',
    ];

    /**
     * Associated Articles.
     *
     * @return \Altek\Eventually\Relations\BelongsToMany
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class)
            ->withPivot('liked')
            ->withTimestamps();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->getAuthIdentifier();
    }

    /**
     * Uppercase first name character accessor.
     *
     * @param string $value
     *
     * @return string
     */
    public function getFirstNameAttribute(string $value): string
    {
        return \ucfirst($value);
    }
}
