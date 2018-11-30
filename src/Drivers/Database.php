<?php

namespace Altek\Accountant\Drivers;

use Altek\Accountant\Contracts\Ledger;
use Altek\Accountant\Contracts\LedgerDriver;
use Altek\Accountant\Contracts\LedgerSigner;
use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Exceptions\AccountantException;
use Illuminate\Support\Facades\Config;

class Database implements LedgerDriver
{
    /**
     * {@inheritdoc}
     */
    public function record(Recordable $model, string $event): Ledger
    {
        $implementation = Config::get('accountant.ledger.implementation', \Altek\Accountant\Models\Ledger::class);

        if (!is_subclass_of($implementation, Ledger::class)) {
            throw new AccountantException(sprintf('Invalid Ledger implementation: "%s"', $implementation));
        }

        $ledger = new $implementation();

        foreach ($data = $model->process($event) as $key => $value) {
            $ledger->setAttribute($key, $value);
        }

        $signer = Config::get('accountant.ledger.signer', \Altek\Accountant\Signers\LedgerSigner::class);

        if (!is_subclass_of($signer, LedgerSigner::class)) {
            throw new AccountantException(sprintf('Invalid LedgerSigner implementation: "%s"', $signer));
        }

        $ledger->setAttribute('signature', call_user_func([$signer, 'sign'], $data))
            ->save();

        return $ledger;
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
