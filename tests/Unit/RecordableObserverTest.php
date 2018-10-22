<?php

namespace Altek\Accountant\Tests;

use Altek\Accountant\RecordableObserver;
use Altek\Accountant\Tests\Models\Article;

class RecordableObserverTest extends AccountantTestCase
{
    /**
     * @group RecordableObserver::retrieved
     * @group RecordableObserver::created
     * @group RecordableObserver::updated
     * @group RecordableObserver::deleted
     * @group RecordableObserver::restoring
     * @group RecordableObserver::restored
     * @test
     *
     * @dataProvider recordableObserverTestProvider
     *
     * @param string $method
     * @param bool   $expectedBefore
     * @param bool   $expectedAfter
     */
    public function itExecutesTheAuditorSuccessfully(string $method, bool $expectedBefore, bool $expectedAfter): void
    {
        $observer = new RecordableObserver();
        $model = factory(Article::class)->create();

        $this->assertSame($expectedBefore, $observer::$restoring);

        $observer->$method($model);

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
                'deleted',
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
        ];
    }
}
