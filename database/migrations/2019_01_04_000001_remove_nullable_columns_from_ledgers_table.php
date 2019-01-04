<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveNullableColumnsFromLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->text('properties')->nullable(false)->change();
            $table->text('modified')->nullable(false)->change();
            $table->text('extra')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->text('properties')->nullable()->change();
            $table->text('modified')->nullable()->change();
            $table->text('extra')->nullable()->change();
        });
    }
}
