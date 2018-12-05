<?php

namespace Altek\Accountant;

use Altek\Accountant\Contracts\Cipher;
use Altek\Accountant\Contracts\Identifiable;
use Altek\Accountant\Contracts\IpAddressResolver;
use Altek\Accountant\Contracts\UrlResolver;
use Altek\Accountant\Contracts\UserAgentResolver;
use Altek\Accountant\Contracts\UserResolver;
use Altek\Accountant\Exceptions\AccountantException;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

trait Recordable
{
    /**
     * Is recording enabled?
     *
     * @var bool
     */
    public static $recordingEnabled = true;

    /**
     * Determine if the observer should be registered.
     *
     * @return bool
     */
    public static function shouldRegisterObserver(): bool
    {
        if (!static::$recordingEnabled) {
            return false;
        }

        if (App::runningInConsole()) {
            return Config::get('accountant.ledger.cli', false);
        }

        return true;
    }

    /**
     * Recordable boot logic.
     *
     * @return void
     */
    public static function bootRecordable(): void
    {
        if (static::shouldRegisterObserver()) {
            static::observe(new RecordableObserver());
        }
    }

    /**
     * Disable Recording.
     *
     * @return void
     */
    public static function disableRecording(): void
    {
        static::$recordingEnabled = false;
    }

    /**
     * Enable Recording.
     *
     * @return void
     */
    public static function enableRecording(): void
    {
        static::$recordingEnabled = true;
    }

    /**
     * {@inheritdoc}
     */
    public function ledgers(): MorphMany
    {
        $implementation = Config::get('accountant.ledger.implementation', Models\Ledger::class);

        return $this->morphMany($implementation, 'recordable');
    }

    /**
     * {@inheritdoc}
     */
    public function isRecordingEnabled(): bool
    {
        return static::$recordingEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isEventRecordable(?string $event): bool
    {
        return in_array($event, $this->getRecordableEvents(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordableEvents(): array
    {
        return $this->recordableEvents ?? Config::get('accountant.events', [
            'created',
            'updated',
            'deleted',
            'restored',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getLedgerThreshold(): int
    {
        return $this->ledgerThreshold ?? Config::get('accountant.ledger.threshold', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getLedgerDriver(): ?string
    {
        return $this->ledgerDriver ?? Config::get('accountant.ledger.driver', 'database');
    }

    /**
     * Resolve the User.
     *
     * @throws \Altek\Accountant\Exceptions\AccountantException
     *
     * @return Identifiable
     */
    protected function resolveUser(): ?Identifiable
    {
        $implementation = Config::get('accountant.resolvers.user');

        if (!is_subclass_of($implementation, UserResolver::class)) {
            throw new AccountantException(sprintf('Invalid UserResolver implementation: "%s"', $implementation));
        }

        return call_user_func([$implementation, 'resolve']);
    }

    /**
     * Resolve the URL.
     *
     * @throws \Altek\Accountant\Exceptions\AccountantException
     *
     * @return string
     */
    protected function resolveUrl(): string
    {
        $implementation = Config::get('accountant.resolvers.url');

        if (!is_subclass_of($implementation, UrlResolver::class)) {
            throw new AccountantException(sprintf('Invalid UrlResolver implementation: "%s"', $implementation));
        }

        return call_user_func([$implementation, 'resolve']);
    }

    /**
     * Resolve the IP Address.
     *
     * @throws \Altek\Accountant\Exceptions\AccountantException
     *
     * @return string
     */
    protected function resolveIpAddress(): string
    {
        $implementation = Config::get('accountant.resolvers.ip_address');

        if (!is_subclass_of($implementation, IpAddressResolver::class)) {
            throw new AccountantException(sprintf('Invalid IpAddressResolver implementation: "%s"', $implementation));
        }

        return call_user_func([$implementation, 'resolve']);
    }

    /**
     * Resolve the User Agent.
     *
     * @throws \Altek\Accountant\Exceptions\AccountantException
     *
     * @return string
     */
    protected function resolveUserAgent(): ?string
    {
        $implementation = Config::get('accountant.resolvers.user_agent');

        if (!is_subclass_of($implementation, UserAgentResolver::class)) {
            throw new AccountantException(sprintf('Invalid UserAgentResolver implementation: "%s"', $implementation));
        }

        return call_user_func([$implementation, 'resolve']);
    }

    /**
     * {@inheritdoc}
     */
    public function process(string $event): array
    {
        if (!$this->isRecordingEnabled()) {
            throw new AccountantException('Recording is not enabled');
        }

        if (!$this->isEventRecordable($event)) {
            throw new AccountantException(sprintf('Invalid event: "%s"', $event));
        }

        $properties = $this->getAttributes();

        // Cipher property values
        foreach ($this->getCiphers() as $property => $implementation) {
            if (!array_key_exists($property, $properties)) {
                throw new AccountantException(sprintf('Invalid property: "%s"', $property));
            }

            if (!is_subclass_of($implementation, Cipher::class)) {
                throw new AccountantException(sprintf('Invalid Cipher implementation: "%s"', $implementation));
            }

            $properties[$property] = call_user_func([$implementation, 'cipher'], $properties[$property]);
        }

        $user = $this->resolveUser();

        $userPrefix = Config::get('accountant.user.prefix', 'user');

        return [
            $userPrefix.'_id'   => $user ? $user->getIdentifier() : null,
            $userPrefix.'_type' => $user ? $user->getMorphClass() : null,
            'event'             => $event,
            'recordable_id'     => $this->getKey(),
            'recordable_type'   => $this->getMorphClass(),
            'properties'        => $properties,
            'modified'          => array_keys($this->getDirty()),
            'extra'             => $this->supplyExtra($event, $properties, $user),
            'url'               => $this->resolveUrl(),
            'ip_address'        => $this->resolveIpAddress(),
            'user_agent'        => $this->resolveUserAgent(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supplyExtra(string $event, array $properties, ?Identifiable $user): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCiphers(): array
    {
        return $this->ciphers ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function isCurrentStateReachable(): bool
    {
        if (!$this->usesTimestamps()) {
            throw new AccountantException('The use of timestamps is required');
        }

        $ledgers = $this->ledgers()->oldest()->get();

        // Unavailable Ledger history
        if ($ledgers->isEmpty()) {
            return false;
        }

        // The first Ledger must be for the created event
        if ($ledgers->first()->event !== 'created') {
            return false;
        }

        // The created at value must match
        $createdAt = $this->getAttributeValue($this->getCreatedAtColumn());

        if ($createdAt->notEqualTo($ledgers->first()->properties[$this->getCreatedAtColumn()])) {
            return false;
        }

        // The updated at value must match
        $updatedAt = $this->getAttributeValue($this->getUpdatedAtColumn());

        if ($updatedAt->notEqualTo($ledgers->last()->properties[$this->getUpdatedAtColumn()])) {
            return false;
        }

        $properties = [];

        foreach ($ledgers as $ledger) {
            // Ledgers cannot be tainted
            if ($ledger->isTainted()) {
                return false;
            }

            // Ignore retrieved events
            if ($ledger->event === 'retrieved') {
                continue;
            }

            $properties[] = $ledger->properties;
        }

        // Finally, compare the current properties with the compiled ones
        return array_merge(...$properties) === $this->getAttributes();
    }
}
