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
            $table->string('billing_type');
            $table->date('next_due_date');
            $table->integer('value');
            $table->string('cycle');
            $table->string('description');
            $table->string('status');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
