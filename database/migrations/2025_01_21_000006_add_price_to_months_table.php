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
        Schema::table('months', function (Blueprint $table) {
            if (!Schema::hasColumn('months', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('months', function (Blueprint $table) {
            if (Schema::hasColumn('months', 'price')) {
                $table->dropColumn('price');
            }
        });
    }
};

