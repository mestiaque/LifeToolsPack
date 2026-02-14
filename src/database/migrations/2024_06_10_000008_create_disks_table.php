<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disks', function (Blueprint $table) {
            $table->id();

            // Columns
            $table->string('code')->unique();
            $table->string('tag')->nullable();
            $table->float('capacity')->nullable();
            $table->float('used')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disks');
    }
};
