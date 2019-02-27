<?php

declare(strict_types=1);

namespace Altek\Accountant\Tests\Unit;

use Altek\Accountant\Ciphers\Base64;
use Altek\Accountant\Ciphers\Bleach;
use Altek\Accountant\Context;
use Altek\Accountant\Contracts\Identifiable;
use Altek\Accountant\Exceptions\AccountantException;
use Altek\Accountant\Models\Ledger;
use Altek\Accountant\Tests\AccountantTestCase;
use Altek\Accountant\Tests\Models\Article;
use Altek\Accountant\Tests\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

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
     *
     * @dataProvider contextResolverProvider
     *
     * @param int  $contexts
     * @param bool $test
     * @param bool $cli
     */
    public function itWillNotRegisterTheRecordableObserverWhenNotInContext(int $contexts, bool $test, bool $cli): void
    {
        $this->app['config']->set('accountant.contexts', $contexts);

        App::shouldReceive('runningUnitTests')
            ->andReturn($test);

        App::shouldReceive('runningInConsole')
            ->andReturn($cli);

        $this->assertFalse(Article::shouldRegisterObserver());
    }

    /**
     * @return array
     */
    public function contextResolverProvider(): array
    {
        return [
            'In Test with CLI and Web allowed' => [
                Context::CLI | Context::WEB,

                // Test
                true,
                false,
            ],

            'In CLI with Test and Web allowed' => [
                Context::TEST | Context::WEB,

                // CLI
                false,
                true,
            ],

            'In Web with Test and CLI allowed' => [
                Context::TEST | Context::CLI,

                // Web
                false,
                false,
            ],

            'In Test with no context allowed' => [
                0,

                // Test
                true,
                false,
            ],

            'In CLI with no context allowed' => [
                0,

                // CLI
                false,
                true,
            ],

            'In Web with no context allowed' => [
                0,

                // Web
                false,
                false,
            ],
        ];
    }

    /**
     * @group Recordable::shouldRegisterObserver
     * @test
     */
    public function itWillNotRegisterTheRecordableObserverDueToClassNotImplementingContextResolverInterface(): void
    {
        $this->app['config']->set('accountant.resolvers.context', self::class);

        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid ContextResolver implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        Article::shouldRegisterObserver();
    }

    /**
     * @group Recordable::disableRecording
     * @group Recordable::shouldRegisterObserver
     * @test
     */
    public function itWillNotRegisterTheRecordableObserverWhenRecordingIsDisabled(): void
    {
        Article::disableRecording();

        $this->assertFalse(Article::shouldRegisterObserver());

        Article::enableRecording();
    }

    /**
     * @group Recordable::shouldRegisterObserver
     * @test
     */
    public function itWillRegisterTheRecordableObserverByDefault(): void
    {
        $this->assertTrue(Article::shouldRegisterObserver());
    }

    /**
     * @group Recordable::getRecordableEvents
     * @test
     */
    public function itReturnsTheDefaultLedgerEvents(): void
    {
        $article = new Article();

        $this->assertArraySubset([
            'created',
            'updated',
            'restored',
            'deleted',
            'forceDeleted',
        ], $article->getRecordableEvents(), true);
    }

    /**
     * @group Recordable::getRecordableEvents
     * @test
     */
    public function itReturnsTheCustomLedgerEventsFromAttribute(): void
    {
        $article = new Article();

        $article->recordableEvents = [
            'deleted',
            'restored',
        ];

        $this->assertArraySubset([
            'deleted',
            'restored',
        ], $article->getRecordableEvents(), true);
    }

    /**
     * @group Recordable::getRecordableEvents
     * @test
     */
    public function itReturnsTheCustomLedgerEventsFromConfig(): void
    {
        $this->app['config']->set('accountant.events', [
            'deleted',
            'restored',
        ]);

        $article = new Article();

        $this->assertArraySubset([
            'deleted',
            'restored',
        ], $article->getRecordableEvents(), true);
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

        $article->recordableEvents = [
            'retrieved',
            'created',
            'updated',
            'restored',
            'deleted',
            'forceDeleted',
            'toggle',
            'sync',
            'existingPivotUpdated',
            'attached',
            'detached',

        ];

        $this->assertTrue($article->isEventRecordable('retrieved'));
        $this->assertTrue($article->isEventRecordable('created'));
        $this->assertTrue($article->isEventRecordable('updated'));
        $this->assertTrue($article->isEventRecordable('restored'));
        $this->assertTrue($article->isEventRecordable('deleted'));
        $this->assertTrue($article->isEventRecordable('forceDeleted'));
        $this->assertTrue($article->isEventRecordable('toggle'));
        $this->assertTrue($article->isEventRecordable('sync'));
        $this->assertTrue($article->isEventRecordable('existingPivotUpdated'));
        $this->assertTrue($article->isEventRecordable('attached'));
        $this->assertTrue($article->isEventRecordable('detached'));
    }

    /**
     * @group Recordable::collect
     * @group Recordable::disableRecording
     * @test
     */
    public function itFailsToCollectDataWhenRecordingIsNotEnabled(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Recording is not enabled');

        $article = new Article();

        $article::disableRecording();

        $article->collect('created');
    }

    /**
     * @group Recordable::collect
     * @group Recordable::enableRecording
     * @test
     */
    public function itFailsToCollectDataWhenAnInvalidLedgerEventIsPassed(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid event: "retrieved"');

        $article = new Article();

        $article::enableRecording();

        $article->collect('retrieved');
    }

    /**
     * @group Recordable::collect
     * @test
     */
    public function itFailsToCollectDataWhenTheContextResolverImplementationIsInvalid(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid ContextResolver implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        $this->app['config']->set('accountant.resolvers.context', self::class);

        $article = new Article();

        $article->collect('created');
    }

    /**
     * @group Recordable::collect
     * @test
     */
    public function itFailsToCollectDataWhenTheIpAddressResolverImplementationIsInvalid(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid IpAddressResolver implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        $this->app['config']->set('accountant.resolvers.ip_address', self::class);

        $article = new Article();

        $article->collect('created');
    }

    /**
     * @group Recordable::collect
     * @test
     */
    public function itFailsToCollectDataWhenTheUrlResolverImplementationIsInvalid(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid UrlResolver implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        $this->app['config']->set('accountant.resolvers.url', self::class);

        $article = new Article();

        $article->collect('created');
    }

    /**
     * @group Recordable::collect
     * @test
     */
    public function itFailsToCollectDataWhenTheUserAgentResolverImplementationIsInvalid(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid UserAgentResolver implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        $this->app['config']->set('accountant.resolvers.user_agent', self::class);

        $article = new Article();

        $article->collect('created');
    }

    /**
     * @group Recordable::collect
     * @test
     */
    public function itFailsToCollectDataWhenTheUserResolverImplementationIsInvalid(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid UserResolver implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        $this->app['config']->set('accountant.resolvers.user', self::class);

        $article = new Article();

        $article->collect('created');
    }

    /**
     * @group Recordable::collect
     * @test
     */
    public function itSuccessfullyReturnsTheCollectedDataForRecording(): void
    {
        $article = factory(Article::class)->make([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'First step: install the Accountant package.',
            'reviewed'     => 1,
            'published_at' => Carbon::now(),
        ]);

        $this->assertCount(12, $data = $article->collect('created'));

        $this->assertArraySubset([
            'user_id'         => null,
            'user_type'       => null,
            'context'         => Context::TEST,
            'event'           => 'created',
            'recordable_id'   => null,
            'recordable_type' => Article::class,
            'properties'      => [
                'title'        => 'Keeping Track Of Eloquent Model Changes',
                'content'      => 'First step: install the Accountant package.',
                'reviewed'     => 1,
                'published_at' => '2012-06-14 15:03:03',
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
     * @group Recordable::collect
     * @test
     *
     * @dataProvider userResolverProvider
     *
     * @param string $guard
     * @param string $driver
     * @param int    $id
     * @param string $type
     */
    public function itSuccessfullyReturnsCollectedDataForRecordingIncludingResolvedUser(
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

        $this->assertCount(12, $data = $article->collect('created'));

        $this->assertArraySubset([
            'user_id'         => $id,
            'user_type'       => $type,
            'context'         => Context::TEST,
            'event'           => 'created',
            'recordable_id'   => null,
            'recordable_type' => Article::class,
            'properties'      => [
                'title'        => 'Keeping Track Of Eloquent Model Changes',
                'content'      => 'First step: install the Accountant package.',
                'reviewed'     => 1,
                'published_at' => '2012-06-14 15:03:03',
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
     * @group Recordable::collect
     * @test
     */
    public function itSuccessfullyReturnsCollectedDataIncludingExtraSupply(): void
    {
        $article = new class() extends Article {
            public function supplyExtra(string $event, array $properties, ?Identifiable $user): array
            {
                return [
                    'slug' => Str::slug($properties['title']),
                ];
            }
        };

        $article->setRawAttributes([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'First step: install the Accountant package.',
            'reviewed'     => 1,
            'published_at' => '2012-06-14 15:03:03',
        ]);

        $this->assertCount(12, $data = $article->collect('created'));

        $this->assertArraySubset([
            'user_id'         => null,
            'user_type'       => null,
            'context'         => Context::TEST,
            'event'           => 'created',
            'recordable_id'   => null,
            'recordable_type' => \get_class($article),
            'properties'      => [
                'title'        => 'Keeping Track Of Eloquent Model Changes',
                'content'      => 'First step: install the Accountant package.',
                'reviewed'     => 1,
                'published_at' => '2012-06-14 15:03:03',
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
     * @group Recordable::collect
     * @test
     */
    public function itFailsToCollectDataWhenUsingInvalidCipherProperty(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid property: "invalid_property"');

        $article = factory(Article::class)->make();

        $article->ciphers = [
            'invalid_property' => Base64::class,
        ];

        $article->collect('created');
    }

    /**
     * @group Recordable::collect
     * @test
     */
    public function itFailsToCollectDataWhenUsingInvalidCipherImplementation(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid Cipher implementation: "Altek\Accountant\Tests\Unit\RecordableTest"');

        $article = factory(Article::class)->make();

        $article->ciphers = [
            'title' => self::class,
        ];

        $article->collect('created');
    }

    /**
     * @group Recordable::collect
     * @test
     */
    public function itSuccessfullyCiphersTheCollectedData(): void
    {
        $article = factory(Article::class)->make([
            'title'        => 'Keeping Track Of Models',
            'content'      => 'N/A',
            'reviewed'     => 0,
            'published_at' => null,
        ]);

        $article->syncOriginal();

        $article->title        = 'Keeping Track Of Eloquent Model Changes';
        $article->content      = 'First step: install the Accountant package.';
        $article->published_at = Carbon::now();
        $article->reviewed     = 1;

        $article->ciphers = [
            'content'  => Bleach::class,
            'reviewed' => Base64::class,
        ];

        $this->assertCount(12, $data = $article->collect('updated'));

        $this->assertArraySubset([
            'user_id'         => null,
            'user_type'       => null,
            'context'         => Context::TEST,
            'event'           => 'updated',
            'recordable_id'   => null,
            'recordable_type' => Article::class,
            'properties'      => [
                'title'        => 'Keeping Track Of Eloquent Model Changes',
                'content'      => '--------------------------------------kage.',
                'published_at' => '2012-06-14 15:03:03',
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

    /**
     * @group Recordable::isCurrentStateReachable
     * @test
     */
    public function itFailsToValidateTheCurrentStateDueToMissingTimestamps(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('The use of timestamps is required');

        $article = factory(Article::class)->make();

        $article->timestamps = false;

        $article->isCurrentStateReachable();
    }

    /**
     * @group Recordable::isCurrentStateReachable
     * @test
     */
    public function itFailsToValidateTheCurrentStateDueToMissingLedgers(): void
    {
        $article = factory(Article::class)->make();

        $this->assertFalse($article->isCurrentStateReachable());
    }

    /**
     * @group Recordable::isCurrentStateReachable
     * @test
     */
    public function itFailsToValidateTheCurrentStateDueToCreatedEventMissingFromFirstLedger(): void
    {
        $this->app['config']->set('accountant.events', [
            'updated',
        ]);

        $article = factory(Article::class)->create();

        $article->update([
            'title' => 'A change was made to the title',
        ]);

        $this->assertFalse($article->isCurrentStateReachable());
    }

    /**
     * @group Recordable::isCurrentStateReachable
     * @test
     */
    public function itFailsToValidateTheCurrentStateDueToCreatedAtValueMismatch(): void
    {
        $this->app['config']->set('accountant.events', []);

        $article = factory(Article::class)->create();

        factory(Ledger::class)->create([
            'event'           => 'created',
            'recordable_type' => Article::class,
            'recordable_id'   => $article->id,
            'properties'      => [
                'created_at' => '2015-10-24 23:11:10',
            ],
        ]);

        $this->assertFalse($article->isCurrentStateReachable());
    }

    /**
     * @group Recordable::isCurrentStateReachable
     * @test
     */
    public function itFailsToValidateTheCurrentStateDueToUpdatedAtValueMismatch(): void
    {
        $this->app['config']->set('accountant.events', []);

        $article = factory(Article::class)->create();

        factory(Ledger::class)->create([
            'event'           => 'created',
            'recordable_type' => Article::class,
            'recordable_id'   => $article->id,
            'properties'      => [
                'created_at' => '2012-06-14 15:03:03',
                'updated_at' => '2015-10-24 23:11:10',
            ],
        ]);

        $this->assertFalse($article->isCurrentStateReachable());
    }

    /**
     * @group Recordable::isCurrentStateReachable
     * @test
     */
    public function itFailsToValidateTheCurrentStateDueToATaintedLedger(): void
    {
        $article = factory(Article::class)->create();

        // Taint the Ledger
        $ledger           = $article->ledgers()->first();
        $ledger->modified = [
            'title',
        ];

        $ledger->save();

        $this->assertFalse($article->isCurrentStateReachable());
    }

    /**
     * @group Recordable::isCurrentStateReachable
     * @test
     */
    public function itFailsToValidateTheCurrentStateDueToPropertyMismatch(): void
    {
        $this->app['config']->set('accountant.events', [
            'created',
        ]);

        $article = factory(Article::class)->create();

        $article->update([
            'content' => 'A change was made to the content',
        ]);

        $this->assertFalse($article->isCurrentStateReachable());
    }

    /**
     * @group Recordable::isCurrentStateReachable
     * @test
     */
    public function itSuccessfullyValidatesTheCurrentState(): void
    {
        $article = factory(Article::class)->create();

        $article->update([
            'content' => 'A change was made to the content',
        ]);

        $article->update([
            'title' => 'A change was made to the title, too!',
        ]);

        $this->assertTrue($article->isCurrentStateReachable());
    }

    /**
     * @group Recordable::isCurrentStateReachable
     * @test
     */
    public function itSuccessfullyValidatesTheCurrentStateWhileIgnoringNonModifyingEvents(): void
    {
        $this->app['config']->set('accountant.events', [
            'created',
            'updated',
            'retrieved',
        ]);

        $article = factory(Article::class)->create();

        $article->update([
            'content' => 'A change was made to the content',
        ]);

        Article::first();

        $article->update([
            'title' => 'A change was made to the title, too!',
        ]);

        $this->assertTrue($article->isCurrentStateReachable());
    }
}
