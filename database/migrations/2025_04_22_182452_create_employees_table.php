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
    Schema::create('employees', function (Blueprint $table) {
        $table->integer('id_conta');
        $table->string('first_name');
        $table->string('last_name');
        $table->string('email')->unique();
        $table->string('phone_number')->nullable();
        $table->string('status');
        $table->string('employee_position');
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('employees');
}
};
