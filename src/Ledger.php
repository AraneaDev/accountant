<?php

namespace Altek\Accountant;

use Altek\Accountant\Contracts\Cipher;
use Altek\Accountant\Contracts\LedgerSigner;
use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Exceptions\AccountantException;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

trait Ledger
{
    /**
     * Ledger data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Metadata attributes.
     *
     * @var array
     */
    protected $metadata = [];

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        return $this->morphTo();
    }

    /**
     * {@inheritdoc}
     */
    public function recordable()
    {
        return $this->morphTo();
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): array
    {
        $userPrefix = Config::get('accountant.user.prefix', 'user');

        // Metadata
        $this->data = [
            'ledger_id'         => $this->getKey(),
            'ledger_event'      => $this->getAttributeFromArray('event'),
            'ledger_url'        => $this->getAttributeFromArray('url'),
            'ledger_ip_address' => $this->getAttributeFromArray('ip_address'),
            'ledger_user_agent' => $this->getAttributeFromArray('user_agent'),
            'ledger_created_at' => $this->serializeDate($this->getAttributeValue($this->getCreatedAtColumn())),
            'ledger_updated_at' => $this->serializeDate($this->getAttributeValue($this->getUpdatedAtColumn())),
            'ledger_signature'  => $this->getAttributeFromArray('signature'),
            'user_id'           => $this->getAttributeFromArray($userPrefix.'_id'),
            'user_type'         => $this->getAttributeFromArray($userPrefix.'_type'),
        ];

        if ($this->user) {
            foreach ($this->user->getArrayableAttributes() as $attribute => $value) {
                $this->data['user_'.$attribute] = $value;
            }
        }

        $this->metadata = array_keys($this->data);

        // Recordable data
        foreach ($this->getDecipheredProperties(false) as $key => $value) {
            $this->data['recordable_'.$key] = $value;
        }

        return $this->data;
    }

    /**
     * Get the formatted property of a model.
     *
     * @param Model  $model
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function getFormattedProperty(Model $model, string $key, $value)
    {
        // Apply defined get mutator
        if ($model->hasGetMutator($key)) {
            return $model->mutateAttribute($key, $value);
        }

        // Cast to native PHP type
        if ($model->hasCast($key)) {
            return $model->castAttribute($key, $value);
        }

        // Honour DateTime attribute
        if ($value !== null && in_array($key, $model->getDates(), true)) {
            return $model->asDateTime($value);
        }

        return $value;
    }

    /**
     * Get Recordable properties (deciphered).
     *
     * @param bool $strict
     *
     * @throws \Altek\Accountant\Exceptions\AccountantException
     * @throws \Altek\Accountant\Exceptions\DecipherException
     *
     * @return array
     */
    protected function getDecipheredProperties(bool $strict = true): array
    {
        $properties = $this->getAttributeValue('properties');

        foreach ($this->recordable->getCiphers() as $key => $implementation) {
            if (!array_key_exists($key, $properties)) {
                throw new AccountantException(sprintf('Invalid property: "%s"', $key));
            }

            if (!is_subclass_of($implementation, Cipher::class)) {
                throw new AccountantException(sprintf('Invalid Cipher implementation: "%s"', $implementation));
            }

            // If strict mode is on, an exception is thrown when there's an attempt to decipher
            // one way ciphered data, otherwise we just skip to the next property value
            if (call_user_func([$implementation, 'isOneWay']) && !$strict) {
                continue;
            }

            $properties[$key] = call_user_func([$implementation, 'decipher'], $properties[$key]);
        }

        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty(string $key)
    {
        if (!array_key_exists($key, $this->data)) {
            throw new AccountantException(sprintf('Invalid property: "%s"', $key));
        }

        $value = $this->data[$key];

        // User property
        if ($this->user && starts_with($key, 'user_')) {
            return $this->getFormattedProperty($this->user, substr($key, 5), $value);
        }

        // Recordable property
        if ($this->recordable && starts_with($key, 'recordable_')) {
            return $this->getFormattedProperty($this->recordable, substr($key, 11), $value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): array
    {
        if (empty($this->data)) {
            $this->compile();
        }

        $metadata = [];

        foreach ($this->metadata as $key) {
            $value = $this->getProperty($key);

            $metadata[$key] = $value instanceof DateTimeInterface
                ? $this->serializeDate($value)
                : $value;
        }

        return $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(bool $all = false): array
    {
        if (empty($this->data)) {
            $this->compile();
        }

        $data = [];

        $properties = $all ? array_keys($this->getAttributeValue('properties')) : $this->getAttributeValue('modified');

        foreach ($properties as $key) {
            $value = $this->getProperty(sprintf('recordable_%s', $key));

            $data[$key] = $value instanceof DateTimeInterface
                ? $this->serializeDate($value)
                : $value;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(bool $strict = true): Recordable
    {
        if ($strict && $this->isTainted()) {
            throw new AccountantException('Extraction failed due to tainted data');
        }

        return $this->recordable->newFromBuilder($this->getDecipheredProperties($strict));
    }

    /**
     * {@inheritdoc}
     */
    public function isTainted(): bool
    {
        $signer = Config::get('accountant.ledger.signer', \Altek\Accountant\Signers\LedgerSigner::class);

        if (!is_subclass_of($signer, LedgerSigner::class)) {
            throw new AccountantException(sprintf('Invalid LedgerSigner implementation: "%s"', $signer));
        }

        // A date mismatch is enough for a record to be considered tainted
        $createdAt = $this->getAttributeValue($this->getCreatedAtColumn());
        $updatedAt = $this->getAttributeValue($this->getUpdatedAtColumn());

        if ($createdAt->notEqualTo($updatedAt)) {
            return true;
        }

        // Exclude properties that were not present when the signing took place
        $properties = array_diff_key($this->attributesToArray(), array_flip([
            $this->getKeyName(),
            $this->getCreatedAtColumn(),
            $this->getUpdatedAtColumn(),
            'signature',
        ]));

        return $this->getAttributeFromArray('signature') !== call_user_func([$signer, 'sign'], $properties);
    }
}
