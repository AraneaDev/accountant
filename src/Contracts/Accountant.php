<?php

namespace Altek\Accountant\Contracts;

interface Accountant
{
    /**
     * Get a LedgerDriver instance.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     *
     * @throws \Altek\Accountant\Exceptions\AccountantException
     * @throws \InvalidArgumentException
     *
     * @return LedgerDriver
     */
    public function ledgerDriver(Recordable $model): LedgerDriver;

    /**
     * Create a Ledger from a Recordable model.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     * @param string                                 $event
     *
     * @throws \Altek\Accountant\Exceptions\AccountantException
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function record(Recordable $model, string $event): void;
}
