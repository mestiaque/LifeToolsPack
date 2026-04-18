<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('her_cycle_profiles', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('her_cycle_profiles', function (Blueprint $table) {
            $table->dropColumn('name_bn');
        });
    }
};
