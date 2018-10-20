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
     * Ledger data resolver.
     *
     * @return array
     */
    public function resolveData(): array;

    /**
     * Get a Ledger data value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getDataValue(string $key);

    /**
     * Get the Ledger metadata.
     *
     * @return array
     */
    public function getMetadata(): array;

    /**
     * Get the Recordable modified attributes.
     *
     * @return array
     */
    public function getModified(): array;

    /**
     * Get the Ledger metadata as JSON.
     *
     * @param int $options
     * @param int $depth
     *
     * @return string
     */
    public function getMetadataAsJson(int $options = 0, int $depth = 512): string;

    /**
     * Get the Recordable modified attributes as JSON.
     *
     * @param int $options
     * @param int $depth
     *
     * @return string
     */
    public function getModifiedAsJson(int $options = 0, int $depth = 512): string;
}
