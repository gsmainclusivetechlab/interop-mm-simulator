<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration adds columns and rename traceparent column at the table transactions
 */
class UpdateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('traceparent', 'trace_id');
            $table->string('type')->after('callback_url');
            $table->json('debitParty')->after('type');
            $table->json('creditParty')->after('debitParty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('trace_id', 'traceparent');
            $table->dropColumn([
                'type',
                'debitParty',
                'creditParty',
            ]);
        });
    }
}
