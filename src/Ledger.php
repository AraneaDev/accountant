<?php

namespace Altek\Accountant;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * {@inheritdoc}
     */
    public function recordable(): MorphTo
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
            'ledger_event'      => $this->event,
            'ledger_url'        => $this->url,
            'ledger_ip_address' => $this->ip_address,
            'ledger_user_agent' => $this->user_agent,
            'ledger_created_at' => $this->serializeDate($this->created_at),
            'ledger_updated_at' => $this->serializeDate($this->updated_at),
            'user_id'           => $this->getAttribute($userPrefix.'_id'),
            'user_type'         => $this->getAttribute($userPrefix.'_type'),
        ];

        if ($this->user) {
            foreach ($this->user->getArrayableAttributes() as $attribute => $value) {
                $this->data['user_'.$attribute] = $value;
            }
        }

        $this->metadata = array_keys($this->data);

        // Recordable data
        foreach ($this->properties as $key => $value) {
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
     * {@inheritdoc}
     */
    public function getProperty(string $key)
    {
        if (!array_key_exists($key, $this->data)) {
            return;
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

        $properties = $all ? array_keys($this->properties) : $this->modified;

        foreach ($properties as $key) {
            $value = $this->getProperty(sprintf('recordable_%s', $key));

            $data[$key] = $value instanceof DateTimeInterface
                ? $this->serializeDate($value)
                : $value;
        }

        return $data;
    }
}
