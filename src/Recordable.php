<?php

namespace Altek\Accountant;

use Altek\Accountant\Contracts\IpAddressResolver;
use Altek\Accountant\Contracts\UrlResolver;
use Altek\Accountant\Contracts\UserAgentResolver;
use Altek\Accountant\Contracts\UserResolver;
use Altek\Accountant\Exceptions\AccountantException;
use Illuminate\Contracts\Auth\Authenticatable;
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
        return in_array($event, $this->getLedgerEvents(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function getLedgerEvents(): array
    {
        return $this->ledgerEvents ?? Config::get('accountant.ledger.events', [
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
     * @throws AccountantException
     *
     * @return Authenticatable
     */
    protected function resolveUser(): ?Authenticatable
    {
        $userResolver = Config::get('accountant.ledger.resolvers.user');

        if (is_subclass_of($userResolver, UserResolver::class)) {
            return call_user_func([$userResolver, 'resolve']);
        }

        throw new AccountantException('Invalid UserResolver implementation');
    }

    /**
     * Resolve the URL.
     *
     * @throws AccountantException
     *
     * @return string
     */
    protected function resolveUrl(): string
    {
        $urlResolver = Config::get('accountant.ledger.resolvers.url');

        if (is_subclass_of($urlResolver, UrlResolver::class)) {
            return call_user_func([$urlResolver, 'resolve']);
        }

        throw new AccountantException('Invalid UrlResolver implementation');
    }

    /**
     * Resolve the IP Address.
     *
     * @throws AccountantException
     *
     * @return string
     */
    protected function resolveIpAddress(): string
    {
        $ipAddressResolver = Config::get('accountant.ledger.resolvers.ip_address');

        if (is_subclass_of($ipAddressResolver, IpAddressResolver::class)) {
            return call_user_func([$ipAddressResolver, 'resolve']);
        }

        throw new AccountantException('Invalid IpAddressResolver implementation');
    }

    /**
     * Resolve the User Agent.
     *
     * @throws AccountantException
     *
     * @return string
     */
    protected function resolveUserAgent(): ?string
    {
        $userAgentResolver = Config::get('accountant.ledger.resolvers.user_agent');

        if (is_subclass_of($userAgentResolver, UserAgentResolver::class)) {
            return call_user_func([$userAgentResolver, 'resolve']);
        }

        throw new AccountantException('Invalid UserAgentResolver implementation');
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

        $user = $this->resolveUser();

        $userPrefix = Config::get('accountant.user.prefix', 'user');

        return $this->postProcess([
            $userPrefix.'_id'   => $user ? $user->getAuthIdentifier() : null,
            $userPrefix.'_type' => $user ? $user->getMorphClass() : null,
            'event'             => $event,
            'recordable_id'     => $this->getKey(),
            'recordable_type'   => $this->getMorphClass(),
            'properties'        => $this->attributes,
            'modified'          => array_keys($this->getDirty()),
            'url'               => $this->resolveUrl(),
            'ip_address'        => $this->resolveIpAddress(),
            'user_agent'        => $this->resolveUserAgent(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess(array $data): array
    {
        return $data;
    }
}