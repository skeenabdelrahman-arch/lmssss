<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('payments', 'discount_code_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreignId('discount_code_id')->nullable()->after('amount')->constrained('discount_codes')->onDelete('set null');
                $table->decimal('original_amount', 10, 2)->nullable()->after('discount_code_id');
                $table->decimal('discount_amount', 10, 2)->default(0)->after('original_amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'discount_code_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['discount_code_id']);
                $table->dropColumn(['discount_code_id', 'original_amount', 'discount_amount']);
            });
        }
    }
};




