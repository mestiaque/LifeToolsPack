<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');             // Transaction title
            $table->decimal('amount', 15, 2);    // Amount (supports decimals)
            $table->text('description')->nullable(); // Optional description
            $table->timestamps();                // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_expenses');
    }
};
