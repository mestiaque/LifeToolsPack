<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('her_cycle_profiles', function (Blueprint $table) {
            $table->text('notify_emails')->nullable()->after('blood_group');
            $table->text('notify_phones')->nullable()->after('notify_emails');
        });
    }

    public function down(): void
    {
        Schema::table('her_cycle_profiles', function (Blueprint $table) {
            $table->dropColumn(['notify_emails', 'notify_phones']);
        });
    }
};
