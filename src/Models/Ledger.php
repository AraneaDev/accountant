<?php

namespace Altek\Accountant\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model implements \Altek\Accountant\Contracts\Ledger
{
    use \Altek\Accountant\Ledger;

    /**
     * {@inheritdoc}
     */
    protected $table = 'ledgers';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'properties' => 'json',
        'modified'   => 'json',
    ];
}
