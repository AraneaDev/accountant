<?php

namespace Altek\Accountant\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Ledger
{
    /**
     * User accountable for the changes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function user(): MorphTo;

    /**
     * Recordable model to which this Ledger belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function recordable(): MorphTo;

    /**
     * Compile data and metadata.
     *
     * @return array
     */
    public function compile(): array;

    /**
     * Get a property value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getProperty(string $key);

    /**
     * Get the Ledger data.
     *
     * @return array
     */
    public function getMetadata(): array;

    /**
     * Get the Recordable data.
     *
     * @param bool $all
     *
     * @return array
     */
    public function getData(bool $all = false): array;

    /**
     * Create a Recordable instance from a Ledger.
     *
     * @param bool $strict
     *
     * @throws \Altek\Accountant\Exceptions\AccountantException
     * @throws \Altek\Accountant\Exceptions\DecipherException
     *
     * @return Recordable
     */
    public function toRecordable(bool $strict = true): Recordable;
}
