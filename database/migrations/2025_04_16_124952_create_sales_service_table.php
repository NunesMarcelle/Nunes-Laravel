<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('sales_service', function (Blueprint $table) {
        $table->id();
        $table->integer('id_conta');
        $table->unsignedBigInteger('service_id');
        $table->unsignedBigInteger('customer_id');
        $table->decimal('price', 10, 2);
        $table->decimal('discount', 10, 2)->default(0.00);
        $table->decimal('total_price', 10, 2);
        $table->timestamps();


    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_service');
    }
};
