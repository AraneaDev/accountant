<?php

declare(strict_types=1);

namespace Altek\Accountant\Tests\Unit;

use Altek\Accountant\RecordableObserver;
use Altek\Accountant\Tests\AccountantTestCase;
use Altek\Accountant\Tests\Models\Article;

class RecordableObserverTest extends AccountantTestCase
{
    private const NONE      = 0;
    private const RESTORING = 1;
    private const TOGGLING  = 2;
    private const SYNCING   = 3;

    /**
     * @group RecordableObserver::retrieved
     * @group RecordableObserver::created
     * @group RecordableObserver::updated
     * @group RecordableObserver::restoring
     * @group RecordableObserver::restored
     * @group RecordableObserver::deleted
     * @group RecordableObserver::forceDeleted
     * @group RecordableObserver::toggled
     * @group RecordableObserver::synced
     * @group RecordableObserver::existingPivotUpdated
     * @group RecordableObserver::attached
     * @group RecordableObserver::detached
     * @test
     *
     * @dataProvider recordableObserverTestProvider
     *
     * @param string $method
     * @param int    $expectedBefore
     * @param int    $expectedAfter
     */
    public function itSuccessfullyExecutesTheAccountant(string $method, int $expectedBefore, int $expectedAfter): void
    {
        $observer = new RecordableObserver();
        $article  = factory(Article::class)->create();

        $this->assertSame($expectedBefore === self::RESTORING, $observer::$restoring);
        $this->assertSame($expectedBefore === self::TOGGLING, $observer::$toggling);
        $this->assertSame($expectedBefore === self::SYNCING, $observer::$syncing);

        $observer->$method($article, 'users', []);

        $this->assertSame($expectedAfter === self::RESTORING, $observer::$restoring);
        $this->assertSame($expectedAfter === self::TOGGLING, $observer::$toggling);
        $this->assertSame($expectedAfter === self::SYNCING, $observer::$syncing);
    }

    /**
     * @return array
     */
    public function recordableObserverTestProvider(): array
    {
        return [
            'Retrieved event' => [
                'retrieved',
                self::NONE,
                self::NONE,
            ],
            'Created event' => [
                'created',
                self::NONE,
                self::NONE,
            ],
            'Updated event' => [
                'updated',
                self::NONE,
                self::NONE,
            ],
            'Restoring event' => [
                'restoring',
                self::NONE,
                self::RESTORING,
            ],
            'Restored event' => [
                'restored',
                self::RESTORING,
                self::NONE,
            ],
            'Deleted event' => [
                'deleted',
                self::NONE,
                self::NONE,
            ],
            'ForceDeleted event' => [
                'forceDeleted',
                self::NONE,
                self::NONE,
            ],
            'Toggling event' => [
                'toggling',
                self::NONE,
                self::TOGGLING,
            ],
            'Toggled event' => [
                'toggled',
                self::TOGGLING,
                self::NONE,
            ],
            'Syncing event' => [
                'syncing',
                self::NONE,
                self::SYNCING,
            ],
            'Synced event' => [
                'synced',
                self::SYNCING,
                self::NONE,
            ],
            'ExistingPivotUpdated event' => [
                'existingPivotUpdated',
                self::NONE,
                self::NONE,
            ],
            'Attached event' => [
                'attached',
                self::NONE,
                self::NONE,
            ],
            'Detached event' => [
                'detached',
                self::NONE,
                self::NONE,
            ],
        ];
    }
}
