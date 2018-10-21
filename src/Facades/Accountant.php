<?php

namespace Altek\Accountant\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Altek\Accountant\Contracts\LedgerDriver ledgerDriver(\Altek\Accountant\Contracts\Recordable $model);
 * @method static void record(\Altek\Accountant\Contracts\Recordable $model, string $event);
 */
class Accountant extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return \Altek\Accountant\Contracts\Accountant::class;
    }
}