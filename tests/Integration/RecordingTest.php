<?php

namespace Altek\Accountant\Tests\Integration;

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
    public function itWillNotRecordModelsWhenRunningFromTheConsole(): void
    {
        $this->app['config']->set('accountant.ledger.cli', false);

        factory(User::class)->create();

        $this->assertSame(1, User::count());
        $this->assertSame(0, Ledger::count());
    }

    /**
     * @test
     */
    public function itWillRecordModelsWhenRunningFromTheConsole(): void
    {
        $this->app['config']->set('accountant.ledger.cli', true);

        factory(User::class)->create();

        $this->assertSame(1, User::count());
        $this->assertSame(1, Ledger::count());
    }

    /**
     * @test
     */
    public function itWillAlwaysRecordModelsWhenNotRunningFromTheConsole(): void
    {
        App::shouldReceive('runningInConsole')
            ->andReturn(false);

        $this->app['config']->set('accountant.ledger.cli', false);

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
