<?php

declare(strict_types=1);

namespace Altek\Accountant\Tests\Integration;

use Altek\Accountant\Context;
use Altek\Accountant\Events\Recording;
use Altek\Accountant\Exceptions\AccountantException;
use Altek\Accountant\Models\Ledger;
use Altek\Accountant\Tests\AccountantTestCase;
use Altek\Accountant\Tests\Models\Article;
use Altek\Accountant\Tests\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use InvalidArgumentException;

class RecordingTest extends AccountantTestCase
{
    /**
     * @test
     */
    public function itWillNotRecordWhenInTestContext(): void
    {
        $this->app['config']->set('accountant.contexts', Context::CLI | Context::WEB);

        factory(User::class)->create();

        $this->assertSame(1, User::count());
        $this->assertSame(0, Ledger::count());
    }

    /**
     * @test
     */
    public function itWillNotRecordWhenInCliContext(): void
    {
        $this->app['config']->set('accountant.contexts', Context::TEST | Context::WEB);

        App::shouldReceive('runningUnitTests')
            ->andReturn(false);

        App::shouldReceive('runningInConsole')
            ->andReturn(true);

        factory(User::class)->create();

        $this->assertSame(1, User::count());
        $this->assertSame(0, Ledger::count());
    }

    /**
     * @test
     */
    public function itWillNotRecordWhenInWebContext(): void
    {
        $this->app['config']->set('accountant.contexts', Context::TEST | Context::CLI);

        App::shouldReceive('runningUnitTests')
            ->andReturn(false);

        App::shouldReceive('runningInConsole')
            ->andReturn(false);

        factory(User::class)->create();

        $this->assertSame(1, User::count());
        $this->assertSame(0, Ledger::count());
    }

    /**
     * @test
     */
    public function itWillNotRecordInAnyContext(): void
    {
        $this->app['config']->set('accountant.contexts', 0b000);

        factory(User::class)->create();

        $this->assertSame(1, User::count());
        $this->assertSame(0, Ledger::count());
    }

    /**
     * @test
     */
    public function itWillRecordWhenInTestContext(): void
    {
        $this->app['config']->set('accountant.contexts', Context::TEST);

        factory(User::class)->create();

        $this->assertSame(1, User::count());
        $this->assertSame(1, Ledger::count());
    }

    /**
     * @test
     */
    public function itWillRecordWhenInCliContext(): void
    {
        $this->app['config']->set('accountant.contexts', Context::CLI);

        App::shouldReceive('runningUnitTests')
            ->andReturn(false);

        App::shouldReceive('runningInConsole')
            ->andReturn(true);

        factory(User::class)->create();

        $this->assertSame(1, User::count());
        $this->assertSame(1, Ledger::count());
    }

    /**
     * @test
     */
    public function itWillRecordWhenInWebContext(): void
    {
        $this->app['config']->set('accountant.contexts', Context::WEB);

        App::shouldReceive('runningUnitTests')
            ->andReturn(false);

        App::shouldReceive('runningInConsole')
            ->andReturn(false);

        factory(User::class)->create();

        $this->assertSame(1, User::count());
        $this->assertSame(1, Ledger::count());
    }

    /**
     * @test
     */
    public function itWillNotRecordTheRetrievedEventByDefault(): void
    {
        $this->assertSame(0, User::count());
        $this->assertSame(0, Ledger::count());

        factory(User::class)->create();

        $this->assertSame(1, User::count());
        $this->assertSame(1, Ledger::count());

        User::first();

        $this->assertSame(1, Ledger::count());
        $this->assertSame(1, User::count());
    }

    /**
     * @test
     */
    public function itWillRecordTheRetrievedEvent(): void
    {
        $this->app['config']->set('accountant.events', [
            'retrieved',
        ]);

        factory(Article::class)->create([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'N/A',
            'published_at' => null,
            'reviewed'     => 0,
        ]);

        $this->assertSame(0, Ledger::count());

        Article::first();

        $this->assertSame(1, Ledger::count());

        $ledger = Ledger::first();

        $this->assertSame('retrieved', $ledger->event);

        $this->assertArraySubset([
            'id'           => '1',
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'N/A',
            'published_at' => null,
            'reviewed'     => '0',
            'updated_at'   => '2012-06-14 15:03:03',
            'created_at'   => '2012-06-14 15:03:03',
            'deleted_at'   => null,
        ], $ledger->properties, true);

        $this->assertEmpty($ledger->modified);
    }

    /**
     * @test
     */
    public function itWillRecordTheCreatedEvent(): void
    {
        $this->app['config']->set('accountant.events', [
            'created',
        ]);

        $this->assertSame(0, Ledger::count());

        factory(Article::class)->create([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'N/A',
            'published_at' => null,
            'reviewed'     => 0,
        ]);

        $this->assertSame(1, Ledger::count());

        $ledger = Ledger::first();

        $this->assertSame('created', $ledger->event);

        $this->assertArraySubset([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'N/A',
            'published_at' => null,
            'reviewed'     => 0,
            'updated_at'   => '2012-06-14 15:03:03',
            'created_at'   => '2012-06-14 15:03:03',
            'id'           => 1,
        ], $ledger->properties, true);

        $this->assertArraySubset([
            'title',
            'content',
            'published_at',
            'reviewed',
            'updated_at',
            'created_at',
            'id',
        ], $ledger->modified, true);
    }

    /**
     * @test
     */
    public function itWillRecordTheUpdatedEvent(): void
    {
        $this->app['config']->set('accountant.events', [
            'updated',
        ]);

        $article = factory(Article::class)->create([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'N/A',
            'published_at' => null,
            'reviewed'     => 0,
        ]);

        $this->assertSame(0, Ledger::count());

        $article->update([
            'content'      => 'First step: install the Accountant package.',
            'published_at' => Carbon::now(),
            'reviewed'     => 1,
        ]);

        $this->assertSame(1, Ledger::count());

        $ledger = Ledger::first();

        $this->assertSame('updated', $ledger->event);

        $this->assertArraySubset([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'First step: install the Accountant package.',
            'published_at' => '2012-06-14 15:03:03',
            'reviewed'     => 1,
            'updated_at'   => '2012-06-14 15:03:03',
            'created_at'   => '2012-06-14 15:03:03',
            'id'           => 1,
        ], $ledger->properties, true);

        $this->assertArraySubset([
            'content',
            'published_at',
            'reviewed',
        ], $ledger->modified, true);
    }

    /**
     * @test
     */
    public function itWillRecordTheDeletedEvent(): void
    {
        $this->app['config']->set('accountant.events', [
            'deleted',
        ]);

        $article = factory(Article::class)->create([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'N/A',
            'published_at' => null,
            'reviewed'     => 0,
        ]);

        $this->assertSame(0, Ledger::count());

        $article->delete();

        $this->assertSame(1, Ledger::count());

        $ledger = Ledger::first();

        $this->assertSame('deleted', $ledger->event);

        $this->assertArraySubset([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'N/A',
            'published_at' => null,
            'reviewed'     => 0,
            'updated_at'   => '2012-06-14 15:03:03',
            'created_at'   => '2012-06-14 15:03:03',
            'id'           => 1,
            'deleted_at'   => '2012-06-14 15:03:03',
        ], $ledger->properties, true);

        $this->assertArraySubset([
            'deleted_at',
        ], $ledger->modified, true);
    }

    /**
     * @test
     */
    public function itWillRecordTheRestoredEvent(): void
    {
        $this->app['config']->set('accountant.events', [
            'restored',
        ]);

        $article = factory(Article::class)->create([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'N/A',
            'published_at' => null,
            'reviewed'     => 0,
        ]);

        $article->delete();

        $this->assertSame(0, Ledger::count());

        $article->restore();

        $this->assertSame(1, Ledger::count());

        $ledger = Ledger::first();

        $this->assertSame('restored', $ledger->event);

        $this->assertArraySubset([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'N/A',
            'published_at' => null,
            'reviewed'     => 0,
            'updated_at'   => '2012-06-14 15:03:03',
            'created_at'   => '2012-06-14 15:03:03',
            'id'           => 1,
            'deleted_at'   => null,
        ], $ledger->properties, true);

        $this->assertEmpty($ledger->modified);
    }

    /**
     * @test
     */
    public function itWillRecordTheToggledEvent(): void
    {
        $this->app['config']->set('accountant.events', [
            'toggled',
        ]);

        $user = factory(User::class)->create();
        factory(Article::class, 2)->create();

        $this->assertSame(0, Ledger::count());

        $user->articles()->toggle([
            2 => [
                'liked' => false,
            ],
            1 => [
                'liked' => true,
            ],
        ]);

        $this->assertSame(1, Ledger::count());

        $ledger = Ledger::first();

        $this->assertSame('toggled', $ledger->event);

        $this->assertEmpty($ledger->modified);

        $this->assertArraySubset([
            'relation'   => 'articles',
            'properties' => [
                2 => [
                    'liked' => false,
                ],
                1 => [
                    'liked' => true,
                ],
            ],
        ], $ledger->getPivotData(), true);
    }

    /**
     * @test
     */
    public function itWillRecordTheSyncedEvent(): void
    {
        $this->app['config']->set('accountant.events', [
            'synced',
        ]);

        $user = factory(User::class)->create();
        factory(Article::class, 2)->create();

        $this->assertSame(0, Ledger::count());

        $user->articles()->sync([
            2 => [
                'liked' => false,
            ],
            1 => [
                'liked' => true,
            ],
        ]);

        $this->assertSame(1, Ledger::count());

        $ledger = Ledger::first();

        $this->assertSame('synced', $ledger->event);

        $this->assertEmpty($ledger->modified);

        $this->assertArraySubset([
            'relation'   => 'articles',
            'properties' => [
                2 => [
                    'liked' => false,
                ],
                1 => [
                    'liked' => true,
                ],
            ],
        ], $ledger->getPivotData(), true);
    }

    /**
     * @test
     */
    public function itWillRecordTheExistingPivotUpdatedEvent(): void
    {
        $this->app['config']->set('accountant.events', [
            'existingPivotUpdated',
        ]);

        $user = factory(User::class)->create();

        $articles = factory(Article::class, 2)->create()->each(function (Article $article) use ($user) {
            $article->users()->attach($user, [
                'liked' => false,
            ]);
        });

        $this->assertSame(0, Ledger::count());

        $user->articles()->updateExistingPivot($articles, [
            'liked' => true,
        ]);

        $this->assertSame(1, Ledger::count());

        $ledger = Ledger::first();

        $this->assertSame('existingPivotUpdated', $ledger->event);

        $this->assertEmpty($ledger->modified);

        $this->assertArraySubset([
            'relation'   => 'articles',
            'properties' => [
                2 => [
                    'liked' => true,
                ],
                1 => [
                    'liked' => true,
                ],
            ],
        ], $ledger->getPivotData(), true);
    }

    /**
     * @test
     */
    public function itWillRecordTheAttachedEvent(): void
    {
        $this->app['config']->set('accountant.events', [
            'attached',
        ]);

        $user = factory(User::class)->create();
        factory(Article::class, 2)->create();

        $this->assertSame(0, Ledger::count());

        $user->articles()->attach([
            2 => [
                'liked' => false,
            ],
            1 => [
                'liked' => true,
            ],
        ]);

        $this->assertSame(1, Ledger::count());

        $ledger = Ledger::first();

        $this->assertSame('attached', $ledger->event);

        $this->assertEmpty($ledger->modified);

        $this->assertArraySubset([
            'relation'   => 'articles',
            'properties' => [
                2 => [
                    'liked' => false,
                ],
                1 => [
                    'liked' => true,
                ],
            ],
        ], $ledger->getPivotData(), true);
    }

    /**
     * @test
     */
    public function itWillRecordTheDetachedEvent(): void
    {
        $this->app['config']->set('accountant.events', [
            'detached',
        ]);

        $user = factory(User::class)->create();

        factory(Article::class, 2)->create()->each(function (Article $article) use ($user) {
            $article->users()->attach($user, [
                'liked' => $article->id === 1,
            ]);
        });

        $this->assertSame(0, Ledger::count());

        $user->articles()->detach();

        $this->assertSame(1, Ledger::count());

        $ledger = Ledger::first();

        $this->assertSame('detached', $ledger->event);

        $this->assertEmpty($ledger->modified);

        $this->assertArraySubset([
            'relation'   => 'articles',
            'properties' => [
                1 => [],
                2 => [],
            ],
        ], $ledger->getPivotData(), true);
    }

    /**
     * @test
     */
    public function itWillKeepAllLedgers(): void
    {
        $this->app['config']->set('accountant.ledger.threshold', 0);
        $this->app['config']->set('accountant.events', [
            'updated',
        ]);

        $article = factory(Article::class)->create([
            'reviewed' => 1,
        ]);

        foreach (range(0, 99) as $count) {
            $article->update([
                'reviewed' => $count % 2,
            ]);
        }

        $this->assertSame(100, $article->ledgers()->count());
    }

    /**
     * @test
     */
    public function itWillRemoveOlderLedgersAboveTheThreshold(): void
    {
        $this->app['config']->set('accountant.ledger.threshold', 10);
        $this->app['config']->set('accountant.events', [
            'updated',
        ]);

        $article = factory(Article::class)->create([
            'reviewed' => 1,
        ]);

        foreach (range(0, 99) as $count) {
            $article->update([
                'reviewed' => $count % 2,
            ]);
        }

        $this->assertSame(10, $article->ledgers()->count());
    }

    /**
     * @test
     */
    public function itWillNotRecordDueToUnsupportedDriver(): void
    {
        $this->app['config']->set('accountant.ledger.driver', 'foo');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Driver [foo] not supported.');

        factory(Article::class)->create();
    }

    /**
     * @test
     */
    public function itWillNotRecordDueToClassNotImplementingDriverInterface(): void
    {
        // We just pass a FQCN that does not implement the LedgerDriver interface
        $this->app['config']->set('accountant.ledger.driver', self::class);

        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('The LedgerDriver contract must be implemented by the driver');

        factory(Article::class)->create();
    }

    /**
     * @test
     */
    public function itWillNotRecordDueToClassNotImplementingLedgerInterface(): void
    {
        // We just pass a FQCN that does not implement the Ledger interface
        $this->app['config']->set('accountant.ledger.implementation', self::class);

        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid Ledger implementation: "Altek\Accountant\Tests\Integration\RecordingTest"');

        factory(Article::class)->create();
    }

    /**
     * @test
     */
    public function itWillNotRecordDueToClassNotImplementingNotaryInterface(): void
    {
        // We just pass a FQCN that does not implement the Notary interface
        $this->app['config']->set('accountant.notary', self::class);

        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid Notary implementation: "Altek\Accountant\Tests\Integration\RecordingTest"');

        factory(Article::class)->create();
    }

    /**
     * @test
     */
    public function itWillRecordUsingTheDefaultDriver(): void
    {
        $this->app['config']->set('accountant.ledger.driver', null);

        factory(Article::class)->create([
            'title'        => 'Keeping Track Of Eloquent Model Changes Using The Fallback Driver',
            'content'      => 'N/A',
            'published_at' => null,
            'reviewed'     => 0,
        ]);

        $ledger = Ledger::first();

        $this->assertArraySubset([
            'title'        => 'Keeping Track Of Eloquent Model Changes Using The Fallback Driver',
            'content'      => 'N/A',
            'published_at' => null,
            'reviewed'     => 0,
            'updated_at'   => '2012-06-14 15:03:03',
            'created_at'   => '2012-06-14 15:03:03',
            'id'           => 1,
        ], $ledger->properties, true);

        $this->assertArraySubset([
            'title',
            'content',
            'published_at',
            'reviewed',
            'updated_at',
            'created_at',
            'id',
        ], $ledger->modified, true);
    }

    /**
     * @test
     */
    public function itWillCancelTheLedgerCreationFromTheEventListener(): void
    {
        Event::listen(Recording::class, function () {
            return false;
        });

        factory(Article::class)->create();

        $this->assertNull(Ledger::first());
    }

    /**
     * @test
     */
    public function itDisablesAndEnablesRecordingBackAgain(): void
    {
        // Recording is enabled by default
        $this->assertTrue(Article::$recordingEnabled);

        factory(Article::class)->create();

        $this->assertSame(1, Article::count());
        $this->assertSame(1, Ledger::count());

        // Disable Recording
        Article::disableRecording();
        $this->assertFalse(Article::$recordingEnabled);

        factory(Article::class)->create();

        $this->assertSame(2, Article::count());
        $this->assertSame(1, Ledger::count());

        // Re-enable Recording
        Article::enableRecording();
        $this->assertTrue(Article::$recordingEnabled);

        factory(Article::class)->create();

        $this->assertSame(2, Ledger::count());
        $this->assertSame(3, Article::count());
    }
}
