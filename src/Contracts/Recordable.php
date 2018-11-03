<?php

namespace Altek\Accountant\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Recordable
{
    /**
     * Recordable Model ledgers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function ledgers(): MorphMany;

    /**
     * Is recording enabled for this model?
     *
     * @return bool
     */
    public function isRecordingEnabled(): bool;

    /**
     * Determine whether an event is recordable.
     *
     * @param string $event
     *
     * @return bool
     */
    public function isEventRecordable(?string $event): bool;

    /**
     * Get the events that trigger a Ledger.
     *
     * @return array
     */
    public function getLedgerEvents(): array;

    /**
     * Get the Ledger threshold.
     *
     * @return int
     */
    public function getLedgerThreshold(): int;

    /**
     * Get the Ledger driver.
     *
     * @return string
     */
    public function getLedgerDriver(): ?string;

    /**
     * Process the Recordable data.
     *
     * @param string $event
     *
     * @throws \Altek\Accountant\Exceptions\AccountantException
     *
     * @return array
     */
    public function process(string $event): array;

    /**
     * Post process the Recordable data.
     *
     * @param array $data
     *
     * @return array
     */
    public function postProcess(array $data): array;

    /**
     * Get property ciphers.
     *
     * @return array
     */
    public function getCiphers(): array;
}
