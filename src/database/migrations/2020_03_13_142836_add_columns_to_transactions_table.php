<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('subType')->nullable()->after('type');
            $table->text('descriptionText')->nullable()->after('subType');
            $table->timestamp('requestDate')->nullable()->after('descriptionText');
            $table->string('requestingOrganisationTransactionReference')->nullable()->after('requestDate');
            $table->string('geoCode')->nullable()->after('requestingOrganisationTransactionReference');
            $table->json('senderKyc')->nullable()->after('creditParty');
            $table->json('recipientKyc')->nullable()->after('senderKyc');
            $table->string('originalTransactionReference')->nullable()->after('recipientKyc');
            $table->string('servicingIdentity')->nullable()->after('originalTransactionReference');
            $table->string('transactionStatus')->after('servicingIdentity');
            $table->string('transactionReceipt')->after('transactionStatus')->nullable();
            $table->json('metadata')->after('transactionReceipt')->nullable();
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
            $table->dropColumn([
                'subType',
                'descriptionText',
                'requestDate',
                'requestingOrganisationTransactionReference' ,
                'geoCode',
                'senderKyc',
                'recipientKyc',
                'originalTransactionReference',
                'servicingIdentity',
                'transactionStatus',
                'transactionReceipt',
                'metadata',
            ]);
        });
    }
}
