<?php

declare(strict_types=1);

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
        $signature = '381b776c460717a27732910bfd69c48e3ebd5e53efacba0d80d0ccfcf474ae17f9d63599604fe87781553a1933fbdf556cd72ebef0088236caad63cbef76f1a0';

        $this->assertSame($signature, Notary::sign($data));
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
                    'pivot'         => [
                        'relation' => 'users',
                        'data'     => [
                            [
                                'article_id' => 1,
                                'user_id'    => 2,
                                'liked'      => false,
                            ],
                            [
                                'liked'      => true,
                                'user_id'    => 1,
                                'article_id' => 1,
                            ],
                        ],
                    ],
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
                    'pivot'           => [
                        'data'     => [
                            [
                                'user_id'    => 2,
                                'liked'      => false,
                                'article_id' => 1,
                            ],
                            [
                                'article_id' => 1,
                                'liked'      => true,
                                'user_id'    => 1,
                            ],
                        ],
                        'relation' => 'users',
                    ],
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
                    'pivot'         => [
                        'relation' => 'users',
                        'data'     => [
                            [
                                'user_id'    => 2,
                                'liked'      => false,
                                'article_id' => 1,
                            ],
                            [
                                'user_id'    => 1,
                                'liked'      => true,
                                'article_id' => 1,
                            ],
                        ],
                    ],
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
                    'pivot'         => [
                        'data'     => [
                            [
                                'article_id' => 1,
                                'user_id'    => 2,
                                'liked'      => false,
                            ],
                            [
                                'article_id' => 1,
                                'liked'      => true,
                                'user_id'    => 1,
                            ],
                        ],
                        'relation' => 'users',
                    ],
                ],
            ],
        ];
    }
}
