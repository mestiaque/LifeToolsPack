<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('her_cycle_profiles', function (Blueprint $table) {
            $table->date('dob')->nullable();
            $table->float('weight')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->float('height')->nullable();
        });
    }

    public function down()
    {
        Schema::table('her_cycle_profiles', function (Blueprint $table) {
            $table->dropColumn(['dob', 'weight', 'blood_group', 'height']);
        });
    }
};
