<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateCustomLoanPaymentPlansForUserMerge extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('custom_loan_payment_plans')) {
            return;
        }

        $hasLegacyLoanColumn = Schema::hasColumn('custom_loan_payment_plans', 'loan_id');
        $needsLegacyUpgrade = $hasLegacyLoanColumn && !Schema::hasColumn('custom_loan_payment_plans', 'loan_user_id');

        // Fresh installs already use loan_user_id from the create migration.
        if (!$hasLegacyLoanColumn && Schema::hasColumn('custom_loan_payment_plans', 'loan_user_id')) {
            return;
        }

        // Add the new planner key used by user-merged planning.
        if ($needsLegacyUpgrade) {
            Schema::table('custom_loan_payment_plans', function (Blueprint $table) {
                $table->unsignedBigInteger('loan_user_id')->nullable()->after('id');
            });
        }

        // Backfill from old loan_id rows if that legacy column exists.
        if ($hasLegacyLoanColumn) {
            DB::statement('UPDATE custom_loan_payment_plans cpp JOIN loans l ON l.id = cpp.loan_id SET cpp.loan_user_id = l.loan_user_id WHERE cpp.loan_user_id IS NULL');
        }

        if ($hasLegacyLoanColumn) {
            Schema::table('custom_loan_payment_plans', function (Blueprint $table) {
                $table->index(['loan_user_id', 'planned_month'], 'clpp_user_month_idx');
                $table->foreign('loan_user_id', 'clpp_user_fk')
                    ->references('id')
                    ->on('loan_users')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('custom_loan_payment_plans')) {
            return;
        }

        if (!Schema::hasColumn('custom_loan_payment_plans', 'loan_user_id')) {
            return;
        }

        Schema::table('custom_loan_payment_plans', function (Blueprint $table) {
            $table->dropForeign('clpp_user_fk');
            $table->dropIndex('clpp_user_month_idx');
            $table->dropColumn('loan_user_id');
        });
    }
}
