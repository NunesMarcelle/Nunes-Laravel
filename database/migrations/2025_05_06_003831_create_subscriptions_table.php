<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('id_conta');
            $table->string('customer_id'); 
            $table->enum('billing_type', ['BOLETO', 'CREDIT_CARD', 'PIX']);
            $table->date('next_due_date');
            $table->decimal('value', 10, 2);
            $table->enum('cycle', ['WEEKLY', 'MONTHLY', 'BIMONTHLY', 'QUARTERLY', 'SEMIANNUALLY', 'YEARLY']);
            $table->string('description');
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'CANCELLED', 'EXPIRED'])->default('ACTIVE');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
