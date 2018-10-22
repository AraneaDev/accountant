<?php

namespace Altek\Accountant\Tests\Models;

use Altek\Accountant\Contracts\Recordable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Recordable, Authenticatable
{
    use \Altek\Accountant\Recordable;
    use \Illuminate\Auth\Authenticatable;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'is_admin' => 'bool',
    ];

    /**
     * Uppercase first name character accessor.
     *
     * @param string $value
     *
     * @return string
     */
    public function getFirstNameAttribute(string $value): string
    {
        return ucfirst($value);
    }
}
