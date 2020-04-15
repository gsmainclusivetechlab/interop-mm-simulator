<?php

use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for transactions table
 */
class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->char('trace_id', 32);
            $table->string('callback_url');
            $table->string('amount', 23);
            $table->char('currency', 3);
            $table->string('type', 256);
            $table->string('subType', 256)->nullable();
            $table->string('descriptionText', 160)->nullable();
            $table->timestamp('requestDate')->nullable();
            $table->string('requestingOrganisationTransactionReference', 256)->nullable();
            $table->string('geoCode', 256)->nullable();
            $table->json('debitParty');
            $table->json('creditParty');
            $table->json('senderKyc')->nullable();
            $table->json('recipientKyc')->nullable();
            $table->string('originalTransactionReference', 256)->nullable();
            $table->string('servicingIdentity', 256)->nullable();
            $table->json('fees')->nullable();
            $table->string('requestingLei', 20)->nullable();
            $table->string('receivingLei', 20)->nullable();
            $table->json('metadata')->nullable();
            $table->json('internationalTransferInformation')->nullable();
            $table->string('transactionStatus');
            $table->string('transactionReceipt', 256)->nullable();
            $table->timestamps();

            $table->primary('trace_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
