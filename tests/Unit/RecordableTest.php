<?php

namespace Altek\Accountant\Tests\Unit;

use Altek\Accountant\Ciphers\Base64;
use Altek\Accountant\Ciphers\Bleach;
use Altek\Accountant\Exceptions\AccountantException;
use Altek\Accountant\Tests\AccountantTestCase;
use Altek\Accountant\Tests\Models\Article;
use Altek\Accountant\Tests\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;

class RecordableTest extends AccountantTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        // Clear morph maps
        Relation::morphMap([], false);
    }

    /**
     * @group Recordable::shouldRegisterObserver
     * @test
     */
    public function itWillNotRegisterTheRecordableObserverWhenRunningFromTheConsole(): void
    {
        $this->app['config']->set('accountant.ledger.cli', false);

        $this->assertFalse(Article::shouldRegisterObserver());
    }

    /**
     * @group Recordable::shouldRegisterObserver
     * @test
     */
    public function itWillRegisterTheRecordableObserverWhenRunningFromTheConsole(): void
    {
        $this->app['config']->set('accountant.ledger.cli', true);

        $this->assertTrue(Article::shouldRegisterObserver());
    }

    /**
     * @group Recordable::shouldRegisterObserver
     * @test
     */
    public function itWillAlwaysRegisterTheRecordableObserverWhenNotRunningFromTheConsole(): void
    {
        App::shouldReceive('runningInConsole')
            ->andReturn(false);

        $this->app['config']->set('accountant.ledger.cli', false);

        $this->assertTrue(Article::shouldRegisterObserver());
    }

    /**
     * @group Recordable::getLedgerEvents
     * @test
     */
    public function itReturnsTheDefaultLedgerEvents(): void
    {
        $article = new Article();

        $this->assertArraySubset([
            'created',
            'updated',
            'deleted',
            'restored',
        ], $article->getLedgerEvents(), true);
    }

    /**
     * @group Recordable::getLedgerEvents
     * @test
     */
    public function itReturnsTheCustomLedgerEventsFromAttribute(): void
    {
        $article = new Article();

        $article->ledgerEvents = [
            'deleted',
            'restored',
        ];

        $this->assertArraySubset([
            'deleted',
            'restored',
        ], $article->getLedgerEvents(), true);
    }

    /**
     * @group Recordable::getLedgerEvents
     * @test
     */
    public function itReturnsTheCustomLedgerEventsFromConfig(): void
    {
        $this->app['config']->set('accountant.ledger.events', [
            'deleted',
            'restored',
        ]);

        $article = new Article();

        $this->assertArraySubset([
            'deleted',
            'restored',
        ], $article->getLedgerEvents(), true);
    }

    /**
     * @group Recordable::isEventRecordable
     * @test
     */
    public function itIsNotARecordableEvent(): void
    {
        $article = new Article();

        $this->assertFalse($article->isEventRecordable('retrieved'));
    }

    /**
     * @group Recordable::isEventRecordable
     * @test
     */
    public function itIsARecordableEvent(): void
    {
        $article = new Article();

        $article->ledgerEvents = [
            'created',
            'updated',
            'deleted',
            'restored',
            'retrieved',
        ];

        $this->assertTrue($article->isEventRecordable('created'));
        $this->assertTrue($article->isEventRecordable('updated'));
        $this->assertTrue($article->isEventRecordable('deleted'));
        $this->assertTrue($article->isEventRecordable('restored'));
        $this->assertTrue($article->isEventRecordable('retrieved'));
    }

    /**
     * @group Recordable::process
     * @test
     */
    public function itFailsWhenRecordingIsNotEnabled(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Recording is not enabled');

        $article = new Article();

        $article::disableRecording();

        $article->process('created');
    }

    /**
     * @group Recordable::process
     * @test
     */
    public function itFailsWhenAnInvalidLedgerEventIsPassed(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid event: "retrieved"');

        $article = new Article();

        $article::enableRecording();

        $article->process('retrieved');
    }

    /**
     * @group Recordable::process
     * @test
     */
    public function itFailsWhenTheIpAddressResolverImplementationIsInvalid(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid IpAddressResolver implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        $this->app['config']->set('accountant.ledger.resolvers.ip_address', self::class);

        $article = new Article();

        $article->process('created');
    }

    /**
     * @group Recordable::process
     * @test
     */
    public function itFailsWhenTheUrlResolverImplementationIsInvalid(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid UrlResolver implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        $this->app['config']->set('accountant.ledger.resolvers.url', self::class);

        $article = new Article();

        $article->process('created');
    }

    /**
     * @group Recordable::process
     * @test
     */
    public function itFailsWhenTheUserAgentResolverImplementationIsInvalid(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid UserAgentResolver implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        $this->app['config']->set('accountant.ledger.resolvers.user_agent', self::class);

        $article = new Article();

        $article->process('created');
    }

    /**
     * @group Recordable::process
     * @test
     */
    public function itFailsWhenTheUserResolverImplementationIsInvalid(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid UserResolver implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        $this->app['config']->set('accountant.ledger.resolvers.user', self::class);

        $article = new Article();

        $article->process('created');
    }

    /**
     * @group Recordable::process
     * @test
     */
    public function itReturnsTheProcessedDataForALedger(): void
    {
        $article = factory(Article::class)->make([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'First step: install the Accountant package.',
            'reviewed'     => 1,
            'published_at' => Carbon::now(),
        ]);

        $this->assertCount(11, $data = $article->process('created'));

        $this->assertArraySubset([
            'user_id'         => null,
            'user_type'       => null,
            'event'           => 'created',
            'recordable_id'   => null,
            'recordable_type' => Article::class,
            'properties'      => [
                'title'        => 'Keeping Track Of Eloquent Model Changes',
                'content'      => 'First step: install the Accountant package.',
                'reviewed'     => 1,
                'published_at' => $article->published_at->toDateTimeString(),
            ],
            'modified' => [
                'title',
                'content',
                'published_at',
                'reviewed',
            ],
            'url'        => 'Command Line Interface',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
            'extra'      => [],
        ], $data, true);
    }

    /**
     * @group Recordable::process
     * @test
     *
     * @dataProvider userResolverProvider
     *
     * @param string $guard
     * @param string $driver
     * @param int    $id
     * @param string $type
     */
    public function itReturnsTheProcessedDataForALedgerIncludingResolvedUser(
        string $guard,
        string $driver,
        int $id = null,
        string $type = null
    ): void {
        $this->app['config']->set('accountant.user.guards', [
            $guard,
        ]);

        $user = factory(User::class)->create();

        $this->actingAs($user, $driver);

        $article = factory(Article::class)->make([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'First step: install the Accountant package.',
            'reviewed'     => 1,
            'published_at' => Carbon::now(),
        ]);

        $this->assertCount(11, $data = $article->process('created'));

        $this->assertArraySubset([
            'user_id'         => $id,
            'user_type'       => $type,
            'event'           => 'created',
            'recordable_id'   => null,
            'recordable_type' => Article::class,
            'properties'      => [
                'title'        => 'Keeping Track Of Eloquent Model Changes',
                'content'      => 'First step: install the Accountant package.',
                'reviewed'     => 1,
                'published_at' => $article->published_at->toDateTimeString(),
            ],
            'modified' => [
                'title',
                'content',
                'published_at',
                'reviewed',
            ],
            'url'        => 'Command Line Interface',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
            'extra'      => [],
        ], $data, true);
    }

    /**
     * @return array
     */
    public function userResolverProvider(): array
    {
        return [
            [
                'api',
                'web',
                null,
                null,
            ],
            [
                'web',
                'api',
                null,
                null,
            ],
            [
                'api',
                'api',
                1,
                User::class,
            ],
            [
                'web',
                'web',
                1,
                User::class,
            ],
        ];
    }

    /**
     * @group Recordable::process
     * @group Recordable::extraLedgerData
     * @test
     */
    public function itIncludesExtraLedgerData(): void
    {
        $article = new class() extends Article {
            protected $table = 'articles';

            public function extraLedgerData(string $event, array $properties, ?Authenticatable $user): array
            {
                return [
                    'slug' => str_slug($properties['title']),
                ];
            }
        };

        $article->setRawAttributes([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'First step: install the Accountant package.',
            'reviewed'     => 1,
            'published_at' => '2012-06-14 15:03:00',
        ]);

        $this->assertCount(11, $data = $article->process('created'));

        $this->assertArraySubset([
            'user_id'         => null,
            'user_type'       => null,
            'event'           => 'created',
            'recordable_id'   => null,
            'recordable_type' => get_class($article),
            'properties'      => [
                'title'        => 'Keeping Track Of Eloquent Model Changes',
                'content'      => 'First step: install the Accountant package.',
                'reviewed'     => 1,
                'published_at' => '2012-06-14 15:03:00',
            ],
            'modified' => [
                'title',
                'content',
                'reviewed',
                'published_at',
            ],
            'url'        => 'Command Line Interface',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
            'extra'      => [
                'slug' => 'keeping-track-of-eloquent-model-changes',
            ],
        ], $data, true);
    }

    /**
     * @group Recordable::process
     * @test
     */
    public function itFailsToProcessWhenAnInvalidPropertyIsSet(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid property: "invalid_property"');

        $article = factory(Article::class)->make();

        $article->ciphers = [
            'invalid_property' => Base64::class,
        ];

        $article->process('created');
    }

    /**
     * @group Recordable::process
     * @test
     */
    public function itFailsWhenTheCipherImplementationIsInvalid(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid Cipher implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        $article = factory(Article::class)->make();

        $article->ciphers = [
            'title' => self::class,
        ];

        $article->process('created');
    }

    /**
     * @group Recordable::process
     * @test
     */
    public function itCiphersTheRecordablePropertiesSuccessfully(): void
    {
        $article = factory(Article::class)->make([
            'title'        => 'Keeping Track Of Models',
            'content'      => 'N/A',
            'reviewed'     => 0,
            'published_at' => null,
        ]);

        $article->syncOriginal();

        $article->title = 'Keeping Track Of Eloquent Model Changes';
        $article->content = 'First step: install the Accountant package.';
        $article->published_at = Carbon::now();
        $article->reviewed = 1;

        $article->ciphers = [
            'content'  => Bleach::class,
            'reviewed' => Base64::class,
        ];

        $this->assertCount(11, $data = $article->process('updated'));

        $this->assertArraySubset([
            'user_id'         => null,
            'user_type'       => null,
            'event'           => 'updated',
            'recordable_id'   => null,
            'recordable_type' => Article::class,
            'properties'      => [
                'title'        => 'Keeping Track Of Eloquent Model Changes',
                'content'      => '--------------------------------------kage.',
                'published_at' => $article->published_at->toDateTimeString(),
                'reviewed'     => 'MQ==',
                'ciphers'      => [
                    'content'  => Bleach::class,
                    'reviewed' => Base64::class,
                ],
            ],
            'modified' => [
                'title',
                'content',
                'published_at',
                'reviewed',
                'ciphers',
            ],
            'url'        => 'Command Line Interface',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
            'extra'      => [],
        ], $data, true);
    }

    /**
     * @group Recordable::getLedgerDriver
     * @test
     */
    public function itReturnsTheDefaultLedgerDriverValue(): void
    {
        $article = new Article();

        $this->assertSame('database', $article->getLedgerDriver());
    }

    /**
     * @group Recordable::getLedgerDriver
     * @test
     */
    public function itReturnsTheCustomLedgerDriverValueFromAttribute(): void
    {
        $article = new Article();

        $article->ledgerDriver = 'RedisDriver';

        $this->assertSame('RedisDriver', $article->getLedgerDriver());
    }

    /**
     * @group Recordable::getLedgerDriver
     * @test
     */
    public function itReturnsTheCustomLedgerDriverValueFromConfig(): void
    {
        $this->app['config']->set('accountant.ledger.driver', 'RedisDriver');

        $article = new Article();

        $this->assertSame('RedisDriver', $article->getLedgerDriver());
    }

    /**
     * @group Recordable::getLedgerThreshold
     * @test
     */
    public function itReturnsTheDefaultLedgerThresholdValue(): void
    {
        $article = new Article();

        $this->assertSame(0, $article->getLedgerThreshold());
    }

    /**
     * @group Recordable::getLedgerThreshold
     * @test
     */
    public function itReturnsTheCustomLedgerThresholdValueFromAttribute(): void
    {
        $article = new Article();

        $article->ledgerThreshold = 10;

        $this->assertSame(10, $article->getLedgerThreshold());
    }

    /**
     * @group Recordable::getLedgerThreshold
     * @test
     */
    public function itReturnsTheCustomLedgerThresholdValueFromConfig(): void
    {
        $this->app['config']->set('accountant.ledger.threshold', 200);

        $article = new Article();

        $this->assertSame(200, $article->getLedgerThreshold());
    }
}
