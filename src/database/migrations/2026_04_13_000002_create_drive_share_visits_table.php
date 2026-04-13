<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriveShareVisitsTable extends Migration
{
    public function up()
    {
        Schema::create('drive_share_visits', function (Blueprint $table) {
            $table->id();
            $table->string('share_type', 20); // file|folder
            $table->unsignedBigInteger('document_id')->nullable();
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->string('share_token', 64)->nullable();
            $table->string('ip_address', 45)->default('unknown');
            $table->text('visited_url')->nullable();
            $table->text('referer')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser', 120)->nullable();
            $table->string('os', 120)->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('device_name', 120)->nullable();
            $table->unsignedBigInteger('visit_count')->default(1);
            $table->timestamp('first_visited_at')->nullable();
            $table->timestamp('last_visited_at')->nullable();
            $table->timestamps();

            $table->index(['share_type', 'share_token']);
            $table->index('document_id');
            $table->index('folder_id');
            $table->unique(['share_type', 'share_token', 'ip_address'], 'drive_share_visits_unique_by_ip');
        });
    }

    public function down()
    {
        Schema::dropIfExists('drive_share_visits');
    }
}
