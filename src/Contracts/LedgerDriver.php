<?php

declare(strict_types=1);

namespace Altek\Accountant\Contracts;

interface LedgerDriver
{
    /**
     * Create a Ledger from a Recordable model.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     * @param string                                 $event
     *
     * @throws \Altek\Accountant\Exceptions\AccountantException
     *
     * @return \Altek\Accountant\Contracts\Ledger
     */
    public function record(Recordable $model, string $event): Ledger;

    /**
     * Remove older ledgers that go over the threshold.
     *
     * @param \Altek\Accountant\Contracts\Recordable $model
     *
     * @return bool
     */
    public function prune(Recordable $model): bool;
}
