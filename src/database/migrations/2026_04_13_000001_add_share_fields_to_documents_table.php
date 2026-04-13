<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShareFieldsToDocumentsTable extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('share_token', 64)->nullable()->unique()->after('size');
            $table->string('share_mode', 20)->nullable()->after('share_token'); // temporary|permanent
            $table->timestamp('share_token_created_at')->nullable()->after('share_mode');
            $table->timestamp('share_token_used_at')->nullable()->after('share_token_created_at');
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropUnique(['share_token']);
            $table->dropColumn([
                'share_token',
                'share_mode',
                'share_token_created_at',
                'share_token_used_at',
            ]);
        });
    }
}
