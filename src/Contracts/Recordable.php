<?php

namespace Altek\Accountant\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
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
     * Surplus data for the Ledger.
     *
     * @param string          $event
     * @param array           $properties
     * @param Authenticatable $user
     *
     * @return array
     */
    public function extraLedgerData(string $event, array $properties, ?Authenticatable $user): array;

    /**
     * Get property ciphers.
     *
     * @return array
     */
    public function getCiphers(): array;
}
