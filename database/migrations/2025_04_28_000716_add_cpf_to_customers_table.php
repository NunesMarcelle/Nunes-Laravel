<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCpfToCustomersTable extends Migration
{
    /**
     * Execute as alterações da migração.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('cpf', 11)->unique()->nullable()->after('email');
        });
    }

    /**
     * Reverte as alterações da migração.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('cpf');
        });
    }
}
