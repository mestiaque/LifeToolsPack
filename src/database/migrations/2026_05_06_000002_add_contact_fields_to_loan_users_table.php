<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactFieldsToLoanUsersTable extends Migration
{
    public function up()
    {
        Schema::table('loan_users', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->string('phone', 30)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
        });
    }

    public function down()
    {
        Schema::table('loan_users', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'address']);
        });
    }
}
