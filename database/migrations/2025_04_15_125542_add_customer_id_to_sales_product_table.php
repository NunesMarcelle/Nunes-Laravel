<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerIdToSalesProductTable extends Migration
{
    public function up()
    {
        Schema::table('sales_product', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->after('id');

            // Caso queira definir uma chave estrangeira para customer_id
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('sales_product', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
}
