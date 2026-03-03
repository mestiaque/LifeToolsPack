<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('her_cycle_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('age')->nullable();
            $table->integer('cycle_length')->default(28);
            $table->integer('period_length')->default(5);
            $table->date('last_period_start')->nullable();
            $table->timestamps();
        });

        Schema::create('her_cycle_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('her_cycle_profiles')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('flow_intensity', ['light', 'medium', 'heavy'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('her_cycle_symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('her_cycle_profiles')->onDelete('cascade');
            $table->date('date');
            $table->json('physical_symptoms')->nullable(); // cramps, bloating, headaches, breast_tenderness
            $table->json('emotional_symptoms')->nullable(); // happy, sad, anxious, irritable, energetic
            $table->integer('sleep_quality')->nullable(); // 1-10
            $table->integer('energy_level')->nullable(); // 1-10
            $table->text('custom_symptoms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('her_cycle_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('her_cycle_profiles')->onDelete('cascade');
            $table->date('predicted_period_start');
            $table->date('predicted_ovulation')->nullable();
            $table->date('fertile_window_start')->nullable();
            $table->date('fertile_window_end')->nullable();
            $table->date('pms_start')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('her_cycle_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('her_cycle_profiles')->onDelete('cascade');
            $table->boolean('period_reminder')->default(true);
            $table->integer('period_reminder_days')->default(2);
            $table->boolean('pms_reminder')->default(true);
            $table->integer('pms_reminder_days')->default(3);
            $table->boolean('fertile_reminder')->default(false);
            $table->boolean('symptom_reminder')->default(false);
            $table->time('reminder_time')->default('09:00:00');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('her_cycle_notifications');
        Schema::dropIfExists('her_cycle_predictions');
        Schema::dropIfExists('her_cycle_symptoms');
        Schema::dropIfExists('her_cycle_periods');
        Schema::dropIfExists('her_cycle_profiles');
    }
};
