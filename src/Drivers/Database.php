<?php

namespace Altek\Accountant\Drivers;

use Altek\Accountant\Contracts\Ledger;
use Altek\Accountant\Contracts\LedgerDriver;
use Altek\Accountant\Contracts\Recordable;
use Illuminate\Support\Facades\Config;

class Database implements LedgerDriver
{
    /**
     * {@inheritdoc}
     */
    public function record(Recordable $model, string $event): Ledger
    {
        $implementation = Config::get('accountant.ledger.implementation', \Altek\Accountant\Models\Ledger::class);

        return call_user_func([$implementation, 'create'], $model->toLedger($event));
    }

    /**
     * {@inheritdoc}
     */
    public function prune(Recordable $model): bool
    {
        if (($threshold = $model->getLedgerThreshold()) > 0) {
            $forRemoval = $model->ledgers()
                ->latest()
                ->get()
                ->slice($threshold)
                ->pluck('id');

            if (!$forRemoval->isEmpty()) {
                return $model->ledgers()
                    ->whereIn('id', $forRemoval)
                    ->delete() > 0;
            }
        }

        return false;
    }
}
