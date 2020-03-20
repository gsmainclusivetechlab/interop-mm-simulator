<?php

use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTransactionStatusInTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('transactionStatus');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('transactionStatus', Transaction::STATUSES)->after('servicingIdentity');
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
            $table->dropColumn('transactionStatus');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transactionStatus')->after('servicingIdentity');
        });
    }
}
