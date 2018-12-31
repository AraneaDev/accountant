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
        $article = factory(Article::class)->create();

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
            [
                'retrieved',
                false,
                false,
            ],
            [
                'created',
                false,
                false,
            ],
            [
                'updated',
                false,
                false,
            ],
            [
                'restoring',
                false,
                true,
            ],
            [
                'restored',
                true,
                false,
            ],
            [
                'deleted',
                false,
                false,
            ],
            [
                'forceDeleted',
                false,
                false,
            ],
            [
                'toggled',
                false,
                false,
            ],
            [
                'synced',
                false,
                false,
            ],
            [
                'existingPivotUpdated',
                false,
                false,
            ],
            [
                'attached',
                false,
                false,
            ],
            [
                'detached',
                false,
                false,
            ],

        ];
    }
}
