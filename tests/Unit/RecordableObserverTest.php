<?php

declare(strict_types=1);

namespace Altek\Accountant\Tests\Unit;

use Altek\Accountant\RecordableObserver;
use Altek\Accountant\Tests\AccountantTestCase;
use Altek\Accountant\Tests\Models\Article;

class RecordableObserverTest extends AccountantTestCase
{
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
     * @param bool   $expectedBefore
     * @param bool   $expectedAfter
     */
    public function itSuccessfullyExecutesTheAccountant(string $method, bool $expectedBefore, bool $expectedAfter): void
    {
        $observer = new RecordableObserver();
        $article  = factory(Article::class)->create();

        $this->assertSame($expectedBefore, $observer::$restoring);

        $observer->$method($article, 'users', []);

        $this->assertSame($expectedAfter, $observer::$restoring);
    }

    /**
     * @return array
     */
    public function recordableObserverTestProvider(): array
    {
        return [
            'Retrieved event' => [
                'retrieved',
                false,
                false,
            ],
            'Created event' => [
                'created',
                false,
                false,
            ],
            'Updated event' => [
                'updated',
                false,
                false,
            ],
            'Restoring event' => [
                'restoring',
                false,
                true,
            ],
            'Restored event' => [
                'restored',
                true,
                false,
            ],
            'Deleted event' => [
                'deleted',
                false,
                false,
            ],
            'ForceDeleted event' => [
                'forceDeleted',
                false,
                false,
            ],
            'Toggled event' => [
                'toggled',
                false,
                false,
            ],
            'Synced event' => [
                'synced',
                false,
                false,
            ],
            'ExistingPivotUpdated event' => [
                'existingPivotUpdated',
                false,
                false,
            ],
            'Attached event' => [
                'attached',
                false,
                false,
            ],
            'Detached event' => [
                'detached',
                false,
                false,
            ],

        ];
    }
}
