<?php

declare(strict_types=1);

namespace Altek\Accountant\Tests\Unit;

use Altek\Accountant\Ciphers\Base64;
use Altek\Accountant\Ciphers\Bleach;
use Altek\Accountant\Context;
use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Exceptions\AccountantException;
use Altek\Accountant\Exceptions\DecipherException;
use Altek\Accountant\Models\Ledger;
use Altek\Accountant\Notary;
use Altek\Accountant\Tests\AccountantTestCase;
use Altek\Accountant\Tests\Models\Article;
use Altek\Accountant\Tests\Models\User;
use Carbon\Carbon;
use DateTimeInterface;

class LedgerTest extends AccountantTestCase
{
    /**
     * @group Ledger::compile
     * @test
     */
    public function itSuccessfullyCompilesLedgerData(): void
    {
        $article = factory(Article::class)->create([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'First step: install the Accountant package.',
            'reviewed'     => 1,
            'published_at' => Carbon::now(),
        ]);

        $ledger = $article->ledgers()->first();

        $this->assertCount(18, $compiled = $ledger->compile());

        $this->assertArraySubset([
            'ledger_id'               => 1,
            'ledger_context'          => Context::TEST,
            'ledger_event'            => 'created',
            'ledger_url'              => 'Command Line Interface',
            'ledger_ip_address'       => '127.0.0.1',
            'ledger_user_agent'       => 'Symfony',
            'ledger_created_at'       => '2012-06-14 15:03:03',
            'ledger_updated_at'       => '2012-06-14 15:03:03',
            'user_id'                 => null,
            'user_type'               => null,
            'recordable_title'        => 'Keeping Track Of Eloquent Model Changes',
            'recordable_content'      => 'First step: install the Accountant package.',
            'recordable_published_at' => '2012-06-14 15:03:03',
            'recordable_reviewed'     => 1,
            'recordable_created_at'   => '2012-06-14 15:03:03',
            'recordable_updated_at'   => '2012-06-14 15:03:03',
            'recordable_id'           => 1,
        ], $compiled, true);

        $this->assertArrayHasKey('ledger_signature', $compiled);
    }

    /**
     * @group Ledger::compile
     * @test
     */
    public function itSuccessfullyCompilesLedgerDataIncludingUserAttributes(): void
    {
        $user = factory(User::class)->create([
            'is_admin'   => 1,
            'first_name' => 'rick',
            'last_name'  => 'Sanchez',
            'email'      => 'rick@wubba-lubba-dub.dub',
        ]);

        $this->actingAs($user);

        $article = factory(Article::class)->create([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'First step: install the Accountant package.',
            'reviewed'     => 1,
            'published_at' => Carbon::now(),
        ]);

        $ledger = $article->ledgers()->first();

        $this->assertCount(24, $compiled = $ledger->compile());

        $this->assertArraySubset([
            'ledger_id'               => 2,
            'ledger_context'          => Context::TEST,
            'ledger_event'            => 'created',
            'ledger_url'              => 'Command Line Interface',
            'ledger_ip_address'       => '127.0.0.1',
            'ledger_user_agent'       => 'Symfony',
            'ledger_created_at'       => '2012-06-14 15:03:03',
            'ledger_updated_at'       => '2012-06-14 15:03:03',
            'user_id'                 => '1',
            'user_type'               => User::class,
            'user_is_admin'           => '1',
            'user_first_name'         => 'rick',
            'user_last_name'          => 'Sanchez',
            'user_email'              => 'rick@wubba-lubba-dub.dub',
            'user_created_at'         => '2012-06-14 15:03:03',
            'user_updated_at'         => '2012-06-14 15:03:03',
            'recordable_title'        => 'Keeping Track Of Eloquent Model Changes',
            'recordable_content'      => 'First step: install the Accountant package.',
            'recordable_published_at' => '2012-06-14 15:03:03',
            'recordable_reviewed'     => 1,
            'recordable_created_at'   => '2012-06-14 15:03:03',
            'recordable_updated_at'   => '2012-06-14 15:03:03',
            'recordable_id'           => 1,
        ], $compiled, true);

        $this->assertArrayHasKey('ledger_signature', $compiled);
    }

    /**
     * @group Ledger::compile
     * @group Ledger::getProperty
     * @test
     */
    public function itReturnsTheAppropriateRecordableDataValues(): void
    {
        $user = factory(User::class)->create([
            'is_admin'   => 1,
            'first_name' => 'rick',
            'last_name'  => 'Sanchez',
            'email'      => 'rick@wubba-lubba-dub.dub',
        ]);

        $this->actingAs($user);

        $ledger = factory(Article::class)->create([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'First step: install the Accountant package.',
            'reviewed'     => 1,
            'published_at' => Carbon::now(),
        ])
        ->ledgers()
        ->first();

        // Compile data, making it available to the getProperty() method
        $this->assertCount(24, $ledger->compile());

        // Mutate value
        $this->assertSame('KEEPING TRACK OF ELOQUENT MODEL CHANGES', $ledger->getProperty('recordable_title'));
        $this->assertSame('Rick', $ledger->getProperty('user_first_name'));

        // Cast value
        $this->assertTrue($ledger->getProperty('user_is_admin'));
        $this->assertTrue($ledger->getProperty('recordable_reviewed'));

        // Date value
        $this->assertInstanceOf(DateTimeInterface::class, $ledger->getProperty('user_created_at'));
        $this->assertInstanceOf(DateTimeInterface::class, $ledger->getProperty('recordable_published_at'));

        // Original value
        $this->assertSame('First step: install the Accountant package.', $ledger->getProperty('recordable_content'));
        $this->assertSame('Sanchez', $ledger->getProperty('user_last_name'));

        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid property: "invalid_property"');

        // Fetch invalid property
        $ledger->getProperty('invalid_property');
    }

    /**
     * @group Ledger::getMetadata
     * @test
     */
    public function itReturnsTheLedgerMetadata(): void
    {
        $ledger = factory(Article::class)->create()->ledgers()->first();

        $this->assertCount(11, $metadata = $ledger->getMetadata());

        $this->assertArraySubset([
            'ledger_id'         => 1,
            'ledger_context'    => Context::TEST,
            'ledger_event'      => 'created',
            'ledger_url'        => 'Command Line Interface',
            'ledger_ip_address' => '127.0.0.1',
            'ledger_user_agent' => 'Symfony',
            'ledger_created_at' => '2012-06-14 15:03:03',
            'ledger_updated_at' => '2012-06-14 15:03:03',
            'user_id'           => null,
            'user_type'         => null,
        ], $metadata, true);

        $this->assertArrayHasKey('ledger_signature', $metadata);
    }

    /**
     * @group Ledger::getMetadata
     * @test
     */
    public function itReturnsTheLedgerMetadataIncludingExtraUserAttributes(): void
    {
        $user = factory(User::class)->create([
            'is_admin'   => 1,
            'first_name' => 'rick',
            'last_name'  => 'Sanchez',
            'email'      => 'rick@wubba-lubba-dub.dub',
        ]);

        $this->actingAs($user);

        $ledger = factory(Article::class)->create()->ledgers()->first();

        $this->assertCount(17, $metadata = $ledger->getMetadata());

        $this->assertArraySubset([
            'ledger_id'         => 2,
            'ledger_context'    => Context::TEST,
            'ledger_event'      => 'created',
            'ledger_url'        => 'Command Line Interface',
            'ledger_ip_address' => '127.0.0.1',
            'ledger_user_agent' => 'Symfony',
            'ledger_created_at' => '2012-06-14 15:03:03',
            'ledger_updated_at' => '2012-06-14 15:03:03',
            'user_id'           => 1,
            'user_type'         => User::class,
            'user_is_admin'     => true,
            'user_first_name'   => 'Rick',
            'user_last_name'    => 'Sanchez',
            'user_email'        => 'rick@wubba-lubba-dub.dub',
            'user_created_at'   => '2012-06-14 15:03:03',
            'user_updated_at'   => '2012-06-14 15:03:03',
        ], $metadata, true);

        $this->assertArrayHasKey('ledger_signature', $metadata);
    }

    /**
     * @group Ledger::getData
     * @test
     */
    public function itOnlyReturnsTheModifiedRecordableData(): void
    {
        $article = factory(Article::class)->create([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'First step: install the Accountant package.',
            'reviewed'     => 1,
            'published_at' => Carbon::now(),
        ]);

        $ledger = $article->ledgers()->first();

        $this->assertCount(7, $data = $ledger->getData());

        $this->assertArraySubset([
            'title'        => 'KEEPING TRACK OF ELOQUENT MODEL CHANGES',
            'content'      => 'First step: install the Accountant package.',
            'published_at' => '2012-06-14 15:03:03',
            'reviewed'     => true,
            'updated_at'   => '2012-06-14 15:03:03',
            'created_at'   => '2012-06-14 15:03:03',
            'id'           => 1,
        ], $data, true);
    }

    /**
     * @group Ledger::getData
     * @test
     */
    public function itReturnsAllTheRecordableData(): void
    {
        $ledger = factory(Ledger::class)->create([
            'event'           => 'updated',
            'recordable_type' => Article::class,
            'properties'      => [
                'title'        => 'Keeping Track Of Eloquent Model Changes',
                'content'      => 'First step: install the Accountant package.',
                'published_at' => '2012-06-18 21:32:34',
                'reviewed'     => true,
                'updated_at'   => '2015-10-24 23:11:10',
                'created_at'   => '2012-06-14 15:03:03',
                'id'           => 1,
            ],
            'modified'        => [
                'content',
            ],
        ]);

        $this->assertCount(1, $ledger->getData());
        $this->assertCount(7, $ledger->getData(true));
    }

    /**
     * @group Ledger::getData
     * @test
     */
    public function itReturnsDecipheredRecordableData(): void
    {
        $article = new class() extends Article {
            protected $ciphers = [
                'title'   => Base64::class,
                'content' => Bleach::class,
            ];
        };

        $ledger = factory(Ledger::class)->create([
            'event'           => 'updated',
            'recordable_type' => get_class($article),
            'properties'      => [
                'title'        => 'S2VlcGluZyBUcmFjayBPZiBFbG9xdWVudCBNb2RlbCBDaGFuZ2Vz',
                'content'      => '--------------------------------------kage.',
                'published_at' => '2012-06-18 21:32:34',
                'reviewed'     => true,
                'updated_at'   => '2015-10-24 23:11:10',
                'created_at'   => '2012-06-14 15:03:03',
                'id'           => 1,
            ],
            'modified'        => [
                'content',
            ],
        ]);

        $this->assertCount(1, $modified = $ledger->getData());
        $this->assertCount(7, $all = $ledger->getData(true));

        $this->assertArraySubset([
            'content' => '--------------------------------------kage.',
        ], $modified, true);

        $this->assertArraySubset([
            'title'        => 'KEEPING TRACK OF ELOQUENT MODEL CHANGES',
            'content'      => '--------------------------------------kage.',
            'published_at' => '2012-06-18 21:32:34',
            'reviewed'     => true,
            'updated_at'   => '2015-10-24 23:11:10',
            'created_at'   => '2012-06-14 15:03:03',
            'id'           => 1,
        ], $all, true);
    }

    /**
     * @group Ledger::getData
     * @test
     */
    public function itCreatesReturnsAllTheRecordableData(): void
    {
        $ledger = factory(Ledger::class)->create([
            'event'           => 'updated',
            'recordable_type' => Article::class,
            'properties'      => [
                'title'        => 'Keeping Track Of Eloquent Model Changes',
                'content'      => 'First step: install the Accountant package.',
                'published_at' => '2012-06-18 21:32:34',
                'reviewed'     => true,
                'updated_at'   => '2015-10-24 23:11:10',
                'created_at'   => '2012-06-14 15:03:03',
                'id'           => 1,
            ],
            'modified'        => [
                'content',
            ],
        ]);

        $this->assertCount(1, $ledger->getData());
        $this->assertCount(7, $ledger->getData(true));
    }

    /**
     * @group Ledger::extract
     * @test
     */
    public function itFailsToExtractARecordableInstanceDueToATaintedLedgerDueToDateMismatch(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Extraction failed due to tainted data');

        $ledger = factory(Ledger::class)->create([
            'created_at' => '1975-07-25 16:25:34',
            'updated_at' => '1981-12-16 03:33:01',
        ]);

        $ledger->extract();
    }

    /**
     * @group Ledger::extract
     * @test
     */
    public function itFailsToExtractARecordableInstanceDueToATaintedLedgerDueToSignatureMismatch(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Extraction failed due to tainted data');

        $ledger = factory(Ledger::class)->create([
            'signature' => '',
        ]);

        $ledger->extract();
    }

    /**
     * @group Ledger::extract
     * @test
     */
    public function itFailsToCompileLedgerDataDueToInvalidProperty(): void
    {
        $article = new class() extends Article {
            protected $ciphers = [
                'invalid_property' => Base64::class,
            ];
        };

        $ledger = factory(Ledger::class)->create([
            'recordable_type' => get_class($article),
        ]);

        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid property: "invalid_property"');

        $ledger->extract();
    }

    /**
     * @group Ledger::extract
     * @test
     */
    public function itFailsToCompileLedgerDataDueToInvalidCipherImplementation(): void
    {
        $article = new class() extends Article {
            protected $ciphers = [
                'title' => AccountantTestCase::class,
            ];
        };

        $ledger = factory(Ledger::class)->create([
            'recordable_type' => get_class($article),
            'properties'      => [
                'title' => 'S2VlcGluZyBUcmFjayBPZiBFbG9xdWVudCBNb2RlbCBDaGFuZ2Vz',
            ],
        ]);

        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid Cipher implementation: "Altek\Accountant\Tests\AccountantTestCase"');

        $ledger->extract();
    }

    /**
     * @group Ledger::extract
     * @test
     */
    public function itFailsToExtractARecordableInstanceFromALedgerInStrictMode(): void
    {
        $article = new class() extends Article {
            protected $ciphers = [
                'title'   => Base64::class,
                'content' => Bleach::class,
            ];
        };

        $ledger = factory(Ledger::class)->create([
            'event'           => 'updated',
            'recordable_type' => get_class($article),
            'properties'      => [
                'title'        => 'S2VlcGluZyBUcmFjayBPZiBFbG9xdWVudCBNb2RlbCBDaGFuZ2Vz',
                'content'      => '--------------------------------------kage.',
                'published_at' => '2012-06-18 21:32:34',
                'reviewed'     => true,
                'updated_at'   => '2015-10-24 23:11:10',
                'created_at'   => '2012-06-14 15:03:03',
                'id'           => 1,
            ],
            'modified'        => [
                'content',
            ],
        ]);

        try {
            $ledger->extract();
        } catch (DecipherException $exception) {
            $this->assertSame('Value deciphering is not supported by this implementation', $exception->getMessage());
            $this->assertSame('--------------------------------------kage.', $exception->getCipheredValue());
        }
    }

    /**
     * @group Ledger::extract
     * @test
     */
    public function itSuccessfullyExtractsARecordableInstanceFromALedger(): void
    {
        $article = new class() extends Article {
            protected $ciphers = [
                'title'   => Base64::class,
                'content' => Bleach::class,
            ];
        };

        $ledger = factory(Ledger::class)->create([
            'event'           => 'updated',
            'recordable_type' => get_class($article),
            'properties'      => [
                'title'        => 'S2VlcGluZyBUcmFjayBPZiBFbG9xdWVudCBNb2RlbCBDaGFuZ2Vz',
                'content'      => '--------------------------------------kage.',
                'published_at' => '2012-06-18 21:32:34',
                'reviewed'     => true,
                'updated_at'   => '2015-10-24 23:11:10',
                'created_at'   => '2012-06-14 15:03:03',
                'id'           => 1,
            ],
            'modified'        => [
                'content',
            ],
        ]);

        $article = $ledger->extract(false);

        $this->assertInstanceOf(Recordable::class, $article);
        $this->assertInstanceOf(Article::class, $article);
    }

    /**
     * @group Ledger::extract
     * @test
     */
    public function itSuccessfullyExtractsARecordableInstanceFromALedgerInStrictMode(): void
    {
        $ledger = factory(Ledger::class)->create([
            'event'           => 'updated',
            'recordable_type' => Article::class,
            'properties'      => [
                'title'        => 'Keeping Track Of Eloquent Model Changes',
                'content'      => 'First step: install the Accountant package.',
                'published_at' => '2012-06-18 21:32:34',
                'reviewed'     => true,
                'updated_at'   => '2015-10-24 23:11:10',
                'created_at'   => '2012-06-14 15:03:03',
                'id'           => 1,
            ],
            'modified'        => [
                'content',
            ],
        ]);

        $article = $ledger->extract();

        $this->assertInstanceOf(Recordable::class, $article);
        $this->assertInstanceOf(Article::class, $article);
    }

    /**
     * @group Ledger::isTainted
     * @test
     */
    public function itFailsToAssertTheRecordIsTaintedDueToMissingTimestamps(): void
    {
        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('The use of timestamps is required');

        $article = factory(Ledger::class)->make();

        $article->timestamps = false;

        $article->isTainted();
    }

    /**
     * @group Ledger::isTainted
     * @test
     */
    public function itFailsToAssertTheRecordIsTaintedDueToInvalidNotaryImplementation(): void
    {
        $ledger = factory(Ledger::class)->create();

        $this->app['config']->set('accountant.notary', self::class);

        $this->expectException(AccountantException::class);
        $this->expectExceptionMessage('Invalid Notary implementation: "Altek\Accountant\Tests\Unit\LedgerTest"');

        $ledger->isTainted();
    }

    /**
     * @group Ledger::isTainted
     * @test
     */
    public function itSuccessfullyAssertsTheRecordIsTaintedDueToMismatchingDates(): void
    {
        $ledger = factory(Ledger::class)->create([
            'updated_at' => '2015-10-24 23:11:10',
            'created_at' => '2012-06-14 15:03:03',
        ]);

        $this->assertTrue($ledger->isTainted());
    }

    /**
     * @group Ledger::isTainted
     * @test
     */
    public function itSuccessfullyAssertsTheRecordIsTaintedDueToSignatureMismatch(): void
    {
        $ledger = factory(Ledger::class)->create([
            'signature' => Notary::sign([
                'completely' => 'different',
                'array'      => 'structure',
                'from'       => 'the',
                'one'        => 'used',
                'when'       => 'signing',
            ]),
        ]);

        $this->assertTrue($ledger->isTainted());
    }

    /**
     * @group Ledger::isTainted
     * @test
     */
    public function itSuccessfullyAssertsTheRecordIsNotTainted(): void
    {
        $ledger = factory(Ledger::class)->create();

        $this->assertFalse($ledger->isTainted());
    }
}
