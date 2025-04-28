<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_product', function (Blueprint $table) {
            $table->string('billingType')->nullable();
            $table->date('dueDate')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sales_product', function (Blueprint $table) {
            $table->dropColumn(['billingType', 'dueDate']);
        });
    }
};
