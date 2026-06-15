<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memorable_days', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->string('category')->nullable(); 
            // e.g. birthday, love, career, travel, etc.
            $table->string('location')->nullable();
            $table->string('image_url')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_private')->default(false);
            $table->boolean('reminder_enabled')->default(false);
            $table->integer('reminder_days_before')->nullable();
            $table->json('tags')->nullable();
            $table->tinyInteger('importance_level')->nullable();
            // 1 = low, 5 = high
            $table->boolean('repeat_yearly')->default(false);
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamps();
            $table->index(['user_id', 'event_date']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memorable_days');
    }
};