<?php

namespace Altek\Accountant\Tests\Unit;

use Altek\Accountant\Context;
use Altek\Accountant\Notary;
use Altek\Accountant\Tests\AccountantTestCase;
use Altek\Accountant\Tests\Models\Article;
use Altek\Accountant\Tests\Models\User;

class NotaryTest extends AccountantTestCase
{
    /**
     * @group Notary::sign
     * @group Notary::validate
     * @test
     *
     * @dataProvider notaryProvider
     *
     * @param array $data
     */
    public function itSuccessfullyValidatesTheSameArrayDataOutOfOrder(array $data): void
    {
        $signature = '6715649568ab2d59e395960e46758b6149497f83773ffdc4a0e25e72bc152a729a845e56e3f44ed4d5fa161374046fe0fe533874ffac1de1f04dc2d9a63a62a9';

        $this->assertTrue(Notary::validate($data, $signature));
    }

    /**
     * @return array
     */
    public function notaryProvider(): array
    {
        return [
            [
                [
                    'properties' => [
                        'title'        => 'Keeping Track Of Eloquent Model Changes',
                        'content'      => 'First step: install the Accountant package.',
                        'reviewed'     => 1,
                        'published_at' => '2012-06-14 15:03:03',
                    ],
                    'user_id'         => 123,
                    'recordable_type' => Article::class,
                    'modified'        => [
                        'reviewed',
                        'title',
                        'published_at',
                        'content',
                    ],
                    'user_type'     => User::class,
                    'context'       => Context::TEST,
                    'event'         => 'created',
                    'recordable_id' => 456,
                    'url'           => 'Command Line Interface',
                    'ip_address'    => '127.0.0.1',
                    'user_agent'    => 'Symfony',
                    'extra'         => [
                        'tags' => [
                            'laravel',
                            'eloquent',
                            'accountant',
                        ],
                    ],
                ],
            ],
            [
                [
                    'user_id'         => 123,
                    'recordable_id'   => 456,
                    'recordable_type' => Article::class,
                    'user_type'       => User::class,
                    'event'           => 'created',
                    'properties'      => [
                        'reviewed'     => 1,
                        'content'      => 'First step: install the Accountant package.',
                        'published_at' => '2012-06-14 15:03:03',
                        'title'        => 'Keeping Track Of Eloquent Model Changes',
                    ],
                    'url'        => 'Command Line Interface',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Symfony',
                    'modified'   => [
                        'reviewed',
                        'content',
                        'title',
                        'published_at',
                    ],
                    'context' => Context::TEST,
                    'extra'   => [
                        'tags' => [
                            'laravel',
                            'accountant',
                            'eloquent',
                        ],
                    ],
                ],
            ],
            [
                [
                    'modified' => [
                        'title',
                        'content',
                        'published_at',
                        'reviewed',
                    ],
                    'user_id'    => 123,
                    'user_type'  => User::class,
                    'context'    => Context::TEST,
                    'properties' => [
                        'content'      => 'First step: install the Accountant package.',
                        'published_at' => '2012-06-14 15:03:03',
                        'reviewed'     => 1,
                        'title'        => 'Keeping Track Of Eloquent Model Changes',
                    ],
                    'url'        => 'Command Line Interface',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Symfony',
                    'extra'      => [
                        'tags' => [
                            'eloquent',
                            'accountant',
                            'laravel',
                        ],
                    ],
                    'event'           => 'created',
                    'recordable_id'   => 456,
                    'recordable_type' => Article::class,
                ],
            ],
            [
                [
                    'context'         => Context::TEST,
                    'event'           => 'created',
                    'recordable_type' => Article::class,
                    'properties'      => [
                        'published_at' => '2012-06-14 15:03:03',
                        'title'        => 'Keeping Track Of Eloquent Model Changes',
                        'content'      => 'First step: install the Accountant package.',
                        'reviewed'     => 1,
                    ],
                    'user_id'   => 123,
                    'user_type' => User::class,
                    'modified'  => [
                        'title',
                        'content',
                        'published_at',
                        'reviewed',
                    ],
                    'extra' => [
                        'tags' => [
                            'accountant',
                            'eloquent',
                            'laravel',
                        ],
                    ],
                    'url'           => 'Command Line Interface',
                    'recordable_id' => 456,
                    'ip_address'    => '127.0.0.1',
                    'user_agent'    => 'Symfony',
                ],
            ],
        ];
    }
}
