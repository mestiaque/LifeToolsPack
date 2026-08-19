<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_financial_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('month_label');       // e.g. "2026-07"
            $table->string('type')->default('loan_payment');
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->date('date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('month_label');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_financial_summaries');
    }
};
