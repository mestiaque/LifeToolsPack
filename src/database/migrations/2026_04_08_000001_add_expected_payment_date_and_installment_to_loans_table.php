<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedInteger('installment')->default(1)->after('date');
            $table->unsignedInteger('completed_installments')->default(0)->after('installment');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['installment', 'completed_installments']);
        });
    }
};
