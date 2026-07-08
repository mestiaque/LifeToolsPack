<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paycycle_salaries', function (Blueprint $table) {
            $table->id();
            $table->string('month_label');                              // e.g. "2026-07"
            $table->decimal('salary_amount', 12, 2);
            $table->date('expected_date');                               // this cycle's expected salary date
            $table->date('received_date')->nullable();                   // actual date once received
            $table->decimal('expected_expense', 12, 2)->nullable();      // manual estimate; null = auto-average from daily expenses
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique('month_label');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paycycle_salaries');
    }
};
