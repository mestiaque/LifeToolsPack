<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomLoanPaymentPlansTable extends Migration
{
    public function up(): void
    {
        Schema::create('custom_loan_payment_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_user_id');
            $table->date('planned_month');
            $table->decimal('planned_amount', 14, 2);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('loan_user_id')
                ->references('id')
                ->on('loan_users')
                ->onDelete('cascade');

            $table->index(['loan_user_id', 'planned_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_loan_payment_plans');
    }
}
