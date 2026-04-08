<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->json('installment_labels')->nullable()->after('completed_installments');
            $table->json('installment_expected_dates')->nullable()->after('installment_labels');
            $table->json('installment_amounts')->nullable()->after('installment_expected_dates');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['installment_labels', 'installment_expected_dates', 'installment_amounts']);
        });
    }
};
