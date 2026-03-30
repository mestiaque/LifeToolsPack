<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanUsersTable extends Migration
{
    public function up()
    {
        Schema::create('loan_users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // just name, no user_id
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loan_users');
    }
}
