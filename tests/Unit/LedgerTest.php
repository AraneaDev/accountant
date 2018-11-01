<?php

namespace Altek\Accountant\Tests;

use Altek\Accountant\Models\Ledger;
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
    public function itCompilesTheLedgerData(): void
    {
        $article = factory(Article::class)->create([
            'title'        => 'Keeping Track Of Eloquent Model Changes',
            'content'      => 'First step: install the Accountant package.',
            'reviewed'     => 1,
            'published_at' => Carbon::now(),
        ]);

        $ledger = $article->ledgers()->first();

        $this->assertCount(16, $compiledData = $ledger->compile());

        $this->assertArraySubset([
            'ledger_id'               => 1,
            'ledger_event'            => 'created',
            'ledger_url'              => 'Command Line Interface',
            'ledger_ip_address'       => '127.0.0.1',
            'ledger_user_agent'       => 'Symfony',
            'ledger_created_at'       => $ledger->created_at->toDateTimeString(),
            'ledger_updated_at'       => $ledger->updated_at->toDateTimeString(),
            'user_id'                 => null,
            'user_type'               => null,
            'recordable_title'        => 'Keeping Track Of Eloquent Model Changes',
            'recordable_content'      => 'First step: install the Accountant package.',
            'recordable_published_at' => $article->published_at->toDateTimeString(),
            'recordable_reviewed'     => 1,
            'recordable_created_at'   => $article->created_at->toDateTimeString(),
            'recordable_updated_at'   => $article->updated_at->toDateTimeString(),
            'recordable_id'           => 1,
        ], $compiledData, true);
    }

    /**
     * @group Ledger::compile
     * @test
     */
    public function itCompilesTheLedgerDataIncludingUserAttributes(): void
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

        $this->assertCount(22, $compiledData = $ledger->compile());

        $this->assertArraySubset([
            'ledger_id'               => 2,
            'ledger_event'            => 'created',
            'ledger_url'              => 'Command Line Interface',
            'ledger_ip_address'       => '127.0.0.1',
            'ledger_user_agent'       => 'Symfony',
            'ledger_created_at'       => $ledger->created_at->toDateTimeString(),
            'ledger_updated_at'       => $ledger->updated_at->toDateTimeString(),
            'user_id'                 => '1',
            'user_type'               => User::class,
            'user_is_admin'           => '1',
            'user_first_name'         => 'rick',
            'user_last_name'          => 'Sanchez',
            'user_email'              => 'rick@wubba-lubba-dub.dub',
            'user_created_at'         => $user->created_at->toDateTimeString(),
            'user_updated_at'         => $user->updated_at->toDateTimeString(),
            'recordable_title'        => 'Keeping Track Of Eloquent Model Changes',
            'recordable_content'      => 'First step: install the Accountant package.',
            'recordable_published_at' => $article->published_at->toDateTimeString(),
            'recordable_reviewed'     => 1,
            'recordable_created_at'   => $article->created_at->toDateTimeString(),
            'recordable_updated_at'   => $article->updated_at->toDateTimeString(),
            'recordable_id'           => 1,
        ], $compiledData, true);
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
        $this->assertCount(22, $ledger->compile());

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

        // Invalid value
        $this->assertNull($ledger->getProperty('invalid_key'));
    }

    /**
     * @group Ledger::getMetadata
     * @test
     */
    public function itReturnsTheLedgerMetadata(): void
    {
        $ledger = factory(Article::class)->create()->ledgers()->first();

        $this->assertCount(9, $metadata = $ledger->getMetadata());

        $this->assertArraySubset([
            'ledger_id'         => 1,
            'ledger_event'      => 'created',
            'ledger_url'        => 'Command Line Interface',
            'ledger_ip_address' => '127.0.0.1',
            'ledger_user_agent' => 'Symfony',
            'ledger_created_at' => $ledger->created_at->toDateTimeString(),
            'ledger_updated_at' => $ledger->updated_at->toDateTimeString(),
            'user_id'           => null,
            'user_type'         => null,
        ], $metadata, true);
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

        $this->assertCount(15, $metadata = $ledger->getMetadata());

        $this->assertArraySubset([
            'ledger_id'         => 2,
            'ledger_event'      => 'created',
            'ledger_url'        => 'Command Line Interface',
            'ledger_ip_address' => '127.0.0.1',
            'ledger_user_agent' => 'Symfony',
            'ledger_created_at' => $ledger->created_at->toDateTimeString(),
            'ledger_updated_at' => $ledger->updated_at->toDateTimeString(),
            'user_id'           => 1,
            'user_type'         => User::class,
            'user_is_admin'     => true,
            'user_first_name'   => 'Rick',
            'user_last_name'    => 'Sanchez',
            'user_email'        => 'rick@wubba-lubba-dub.dub',
            'user_created_at'   => $user->created_at->toDateTimeString(),
            'user_updated_at'   => $user->updated_at->toDateTimeString(),
        ], $metadata, true);
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

        $this->assertCount(7, $modified = $ledger->getData());

        $this->assertArraySubset([
            'title'        => 'KEEPING TRACK OF ELOQUENT MODEL CHANGES',
            'content'      => 'First step: install the Accountant package.',
            'published_at' => $article->published_at->toDateTimeString(),
            'reviewed'     => true,
            'updated_at'   => $article->updated_at->toDateTimeString(),
            'created_at'   => $article->created_at->toDateTimeString(),
            'id'           => 1,
        ], $modified, true);
    }

    /**
     * @group Ledger::getData
     * @test
     */
    public function itReturnsAllTheRecordableData(): void
    {
        $ledger = factory(Ledger::class)->create([
            'event'           => 'updated',
            'recordable_id'   => 1,
            'recordable_type' => Article::class,
            'properties'      => [
                'title'        => 'KEEPING TRACK OF ELOQUENT MODEL CHANGES',
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

        $this->assertCount(1, $modified = $ledger->getData());
        $this->assertCount(7, $modified = $ledger->getData(true));
    }
}
